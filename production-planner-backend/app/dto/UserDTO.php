<?php

namespace App\DTO;

/**
 * Data Transfer Object (DTO) for representing a user.
 */
class UserDTO
{
    private string $id;
    private string $email;
    private string $password;

    public function __construct(string $id, string $email, string $password)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
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
            $data['password']
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