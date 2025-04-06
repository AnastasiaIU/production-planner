<?php

namespace App\Controllers;

use App\enums\Role;
use App\Models\UserModel;
use App\Services\ResponseService;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use InvalidArgumentException;
use Throwable;

/**
 * Controller class for handling user-related operations.
 */
class UserController extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Handles user registration.
     */
    public function register(): void
    {
        $data = $this->decodePostData(); // Use base controller method to get POST data
        $this->validateInput(['email', 'password'], $data); // Use base controller validation

        try {
            $this->validateEmail($data['email']);

            // Check if email already exists
            if ($this->userModel->findByEmail($data['email'])) {
                ResponseService::Error('Email already exists', 400);
                return;
            }

            // create user
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $this->userModel->create($data['email'], $hashedPassword, Role::REGULAR);

            ResponseService::Send(['message' => 'User registered successfully']);
            return;
        } catch (Throwable $th) {
            error_log($th->getMessage());
            ResponseService::Error('Registration failed');
            return;
        }
    }

    /**
     * Handles user creation.
     */
    public function create(): void
    {
        $data = $this->decodePostData(); // Use base controller method to get POST data
        $this->validateInput(['email', 'password', 'role'], $data); // Use base controller validation

        try {
            $this->validateEmail($data['email']);

            // Check if email already exists
            if ($this->userModel->findByEmail($data['email'])) {
                ResponseService::Error('Email already exists', 400);
                return;
            }

            // create user
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $role = Role::from($data['role']);
            $user = $this->userModel->create($data['email'], $hashedPassword, $role);

            ResponseService::Send($user);
            return;
        } catch (Throwable $th) {
            error_log($th->getMessage());
            ResponseService::Error('User creation failed');
            return;
        }
    }

    /**
     * Handles user login and returns a JWT token on success.
     */
    public function login(): void
    {
        // Get and parse the JSON request body using base controller method
        $data = $this->decodePostData();

        // Validate that required fields (email & password) exist in request
        $this->validateInput(['email', 'password'], $data);

        // Try to find user with the provided email
        $user = $this->userModel->findByEmail($data['email']);

        // Check if user exists and password matches
        // password_verify securely compares the provided password against the stored hash
        if (!$user || !$user->verifyPassword($data['password'])) {
            ResponseService::Error('Invalid credentials', 401);
            return;
        }

        // Generate a JWT token containing user data
        $token = $this->generateJWT($user);

        // Return the token in the response
        ResponseService::Send(['token' => $token]);
    }

    /**
     * Returns the currently authenticated user’s details.
     */
    public function me(): void
    {
        ResponseService::Send($this->getAuthenticatedUser());
    }

    /**
     * Generates a JWT token for the given user.
     */
    private function generateJWT($user): string
    {
        $issuedAt = time();
        $expire = $issuedAt + 3600 * 4; // 4 hours

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()->value
            ]
        ];

        return JWT::encode($payload, $_ENV["JWT_SECRET"], 'HS256');
    }

    /**
     * Validates that the current authenticated user matches the given user ID.
     */
    public function isMe($id): void
    {
        $this->validateIsMe($id);
        ResponseService::Send(['message' => 'You are authorized to access this resource']);
    }

    /**
     * Restricts access to a specific role.
     *
     * @param Role $requiredRole Required role (e.g., Role::ADMIN)
     */
    public function requireRole(Role $requiredRole): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user->role !== $requiredRole->value) {
            ResponseService::Error("Forbidden: Insufficient privileges", 403);
            exit();
        }
    }

    /**
     * Parses and validates the JWT token from headers.
     */
    public function getAuthenticatedUser()
    {
        // Get all HTTP headers from the request
        $headers = getallheaders();

        // Check if Authorization header exists in the request
        if (!isset($headers['Authorization'])) {
            ResponseService::Error('No token provided', 401);
        }

        // Remove 'Bearer ' prefix from the Authorization header to get the raw token
        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            // Verify and decode the JWT token using the secret key
            // If token is invalid or expired, this will throw an exception
            $token_data = JWT::decode($token, new Key($_ENV["JWT_SECRET"], 'HS256'));
            return $token_data->user;
        } catch (Exception $e) {
            // Return 401 Unauthorized if token is invalid
            ResponseService::Error('Invalid token', 401);
        }
    }

    /**
     * Verifies that the JWT-authenticated user is the same as the ID in request.
     */
    public function validateIsMe($id)
    {
        // Get the authenticated user from the JWT token
        $user = $this->getAuthenticatedUser();

        // Check if the authenticated user's ID matches the requested resource ID
        // Cast the requested ID to integer to ensure type-safe comparison
        if (empty($user) || $user->id !== (int)$id) {
            // Return 403 Forbidden if user tries to access another user's resource
            ResponseService::Error('You are not authorized to access this resource', 403);
            exit();
        }

        return $user;
    }

    /**
     * Retrieves a list of all users, optionally paginated.
     */
    public function getAll(): void
    {
        try {
            $page = $_GET["page"] ?? null;
            $limit = $_GET["limit"] ?? null;

            if ($page && $limit) {
                $limit = (int)$limit;
                $offset = ((int)$page - 1) * $limit;
                ResponseService::Send($this->userModel->getAllPaginated($offset, $limit));
            } else {
                ResponseService::Send($this->userModel->getAll());
            }
        } catch (Throwable $th) {
            ResponseService::Error('Server error: ' . $th->getMessage());
        }
    }

    /**
     * Retrieves a user by their id.
     *
     * @param string $id The id of the user to retrieve.
     */
    public function get(string $id): void
    {
        try {
            $user = $this->userModel->findById((int)$id);

            if (!$user) {
                ResponseService::Error('User not found', 404);
                return;
            }

            ResponseService::Send($user);

        } catch (Throwable $th) {
            ResponseService::Error('Server error: ' . $th->getMessage());
        }
    }

    /**
     * Updates a user in the database.
     *
     * @param string $userId The ID of the user to update.
     */
    public function update(string $userId): void
    {
        $data = $this->decodePostData(); // Use base controller method to get POST data
        $this->validateInput(['email', 'role'], $data); // Use base controller validation

        try {
            $this->validateEmail($data['email']);

            $userByEmail = $this->userModel->findByEmail($data['email']);
            $userById = $this->userModel->findById($userId);

            // Check if email already exists
            if ($userByEmail && $userById->getEmail() !== $data['email']) {
                ResponseService::Error('Email already exists', 400);
                return;
            }

            if (isset($data['password'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            } else {
                $hashedPassword = null;
            }

            $role = Role::from($data['role']);

            $user = $this->userModel->update($userId, $data['email'], $hashedPassword, $role);

            ResponseService::Send($user);

            return;
        } catch (Throwable $th) {
            error_log($th->getMessage());
            ResponseService::Error('Server error: ' . $th->getMessage());
            return;
        }
    }

    /**
     * Deletes a user from the database.
     *
     * @param string $userId The ID of the user to delete.
     */
    public function delete(string $userId): void
    {
        try {
            $this->userModel->delete($userId);
            ResponseService::Send(true);
        } catch (Throwable $th) {
            error_log($th->getMessage());
            ResponseService::Error('Server error: ' . $th->getMessage());
            return;
        }
    }

    /**
     * Validates email format, length, and domain DNS.
     *
     * @param string $email Email to validate
     * @throws InvalidArgumentException
     */
    private function validateEmail(string $email): void
    {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        // Additional validation for email length and domain
        if (strlen($email) > 254) {
            throw new InvalidArgumentException('Email is too long');
        }

        // Extract domain and validate
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain)) {
            throw new InvalidArgumentException('Invalid email domain');
        }
    }
}