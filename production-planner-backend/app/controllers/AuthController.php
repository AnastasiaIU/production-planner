<?php

namespace App\Controllers;

use App\enums\Role;
use App\Models\UserModel;
use App\Services\ResponseService;
use Firebase\JWT\JWT;
use InvalidArgumentException;
use Throwable;

class AuthController extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function register(): void
    {
        $data = $this->decodePostData(); // Use base controller method to get POST data
        $this->validateInput(['email', 'password'], $data); // Use base controller validation

        try {
            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Invalid email format');
            }

            // Additional validation for email length and domain
            if (strlen($data['email']) > 254) {
                throw new InvalidArgumentException('Email is too long');
            }

            // Extract domain and validate
            $domain = substr(strrchr($data['email'], "@"), 1);
            if (!checkdnsrr($domain)) {
                throw new InvalidArgumentException('Invalid email domain');
            }

            // Check if email already exists
            if ($this->userModel->findByEmail($data['email'])) {
                ResponseService::Error('Email already exists', 400);
                return;
            }

            // create user
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $role = isset($data['role']) ? Role::from($data['role']) : Role::REGULAR; // Default to 'Regular' if role is not provided
            $this->userModel->create($data['email'], $hashedPassword, $role);
            ResponseService::Send(['message' => 'User registered successfully']);
            return;
        } catch (Throwable $th) {
            error_log($th->getMessage());
            ResponseService::Error('Registration failed');
            return;
        }
    }

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

    public function me(): void
    {
        ResponseService::Send($this->getAuthenticatedUser());
    }

    private function generateJWT($user): string
    {
        $issuedAt = time();
        $expire = $issuedAt + 3600 * 4; // 4 hours

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ]
        ];

        return JWT::encode($payload, $_ENV["JWT_SECRET"], 'HS256');
    }

    public function isMe($id): void
    {
        $this->validateIsMe($id);
        ResponseService::Send(['message' => 'You are authorized to access this resource']);
    }
}
