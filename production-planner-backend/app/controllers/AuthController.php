<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Services\ResponseService;
use Exception;
use Firebase\JWT\JWT;

class AuthController extends Controller
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

        // Check if email already exists
        if ($this->userModel->findByEmail($data['email'])) {
            ResponseService::Error('Email already exists', 400);
            return;
        }

        // create user
        try {
            $this->userModel->create($data['email'], $data['password']);
            ResponseService::Send(['message' => 'User registered successfully']);
            return;
        } catch (Exception $e) {
            var_dump($e);
            ResponseService::Error('Registration failed', 500);
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
        if (!$user || !password_verify($data['password'], $user['password'])) {
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
                'id' => $user['id'],
                'email' => $user['email']
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
