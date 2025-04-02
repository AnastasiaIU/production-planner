<?php

namespace App\DTO;

use App\enums\Role;

/**
 * Data Transfer Object (DTO) for representing a user.
 */
class UserDTO
{
    private string $id;
    private string $email;
    private string $password;
    private Role $role;

    public function __construct(string $id, string $email, string $password, Role $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getEmail(): string {
        return $this->email;
    }

    /**
     * Creates an UserDTO instance from an associative array.
     *
     * @param array $data The associative array containing user data.
     * @return self A new instance of UserDTO populated with the provided data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['email'],
            $data['password'],
            Role::from($data['role'])
        );
    }

    /**
     * Verifies if the provided password matches the stored hashed password.
     *
     * @param string $password The plain text password to verify.
     * @return bool True if the password matches, false otherwise.
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}