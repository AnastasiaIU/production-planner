<?php

namespace App\Models;

use App\DTO\UserDTO;
use App\enums\Role;
use PDO;
use Throwable;

/**
 * User class extends Base to interact with the USER entity in the database.
 */
class UserModel extends BaseModel
{
    /**
     * Creates a new user in the database.
     *
     * @param string $email The email of the user to create.
     * @param string $password The hashed password of the user to create.
     * @param Role $role The role of the new user.
     *
     * @return UserDTO|null The data transfer object representing the created user or null if the creation fails.
     */
    public function create(string $email, string $password, Role $role): ?UserDTO {
        try {
            self::$pdo->beginTransaction();

            $query = self::$pdo->prepare('INSERT INTO USER (email, password, role) VALUES (:email, :password, :role)');

            $success = $query->execute([
                ':email' => $email,
                ':password' => $password,
                ':role' => $role->value
            ]);

            if (!$success) {
                self::$pdo->rollBack(); // Revert changes if insertion fails
                exit;
            }

            $userId = self::$pdo->lastInsertId();

            self::$pdo->commit();

            return $this->findById($userId);

        } catch (Throwable $th) {
            self::$pdo->rollBack();
            error_log($th->getMessage());
        }

        return null;
    }

    /**
     * Retrieves a user by their email.
     *
     * @param string $email The email of the user to retrieve.
     * @return UserDTO|null The data transfer object representing the user or null if the user is not found.
     */
    public function findByEmail(string $email): ?UserDTO
    {
        $query = self::$pdo->prepare("SELECT id, email, password, role
                                            FROM USER WHERE email = :email");
        $query->execute([':email' => $email]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        return UserDTO::fromArray($user);
    }

    /**
     * Retrieves a user by their id.
     *
     * @param int $id The id of the user to retrieve.
     * @return UserDTO|null The data transfer object representing the user or null if the user is not found.
     */
    public function findById(int $id): ?UserDTO
    {
        $query = self::$pdo->prepare("SELECT id, email, password, role
                                            FROM USER WHERE id = :id");
        $query->execute([':id' => $id]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        return UserDTO::fromArray($user);
    }
}