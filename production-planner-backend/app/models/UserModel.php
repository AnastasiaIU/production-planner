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
     * Fetches all users.
     *
     * @return array An array of user objects.
     */
    public function getAll(): array
    {
        $query = self::$pdo->prepare(
            'SELECT id, email, password, role
                    FROM `USER`'
        );
        $query->execute();
        $users = $query->fetchAll(PDO::FETCH_ASSOC);
        $dtos = [];

        foreach ($users as $user) {
            $dto = UserDTO::fromArray($user);
            $dtos[] = $dto;
        }

        return $dtos;
    }

    /**
     * Retrieves a paginated list of users.
     *
     * This method queries the database for users,
     * applying the provided offset and limit for pagination.
     *
     * @param int $offset The number of records to skip (used for pagination).
     * @param int $limit The maximum number of records to return.
     *
     * @return array An array of UserDTO objects.
     */
    public function getAllPaginated(int $offset, int $limit): array
    {
        $query = self::$pdo->prepare(
            'SELECT id, email, password, role
                    FROM `USER`
                    LIMIT :offset, :limit'
        );

        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);

        $query->execute();

        $users = $query->fetchAll(PDO::FETCH_ASSOC);
        $dtos = [];

        foreach ($users as $user) {
            $dto = UserDTO::fromArray($user);
            $dtos[] = $dto;
        }

        return $dtos;
    }

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

    /**
     * Updates a user in the database.
     *
     * @param string $userId The ID of the user to update.
     * @param string $email The updated email of the user.
     * @param string|null $password The updated hashed password of the user or null if the password has not changed.
     * @param Role $role The updated role of the user.
     * @return UserDTO|null The data transfer object representing the updated user
     * or null if the operation has failed.
     */
    public function update(string $userId, string $email, ?string $password, Role $role): ?UserDTO
    {
        try {
            // Begin transaction
            self::$pdo->beginTransaction();

            if ($password === null) {
                $query = self::$pdo->prepare(
                    'UPDATE `USER` SET email = :email, role = :role WHERE id = :userId'
                );
                $query->execute([
                    ":email" => $email,
                    ":role" => $role->value,
                    ":userId" => $userId
                ]);
            } else {
                $query = self::$pdo->prepare(
                    'UPDATE `USER` SET email = :email, password = :password, role = :role WHERE id = :userId'
                );
                $query->execute([
                    ":email" => $email,
                    ":password" => $password,
                    ":role" => $role->value,
                    ":userId" => $userId
                ]);
            }
            $query->closeCursor();

            $user = $this->findById($userId);

            // Commit transaction
            self::$pdo->commit();

            return $user;
        } catch (Throwable $th) {
            // Rollback transaction in case of error
            self::$pdo->rollBack();
            error_log($th->getMessage());
            return null;
        }
    }

    /**
     * Deletes a user from the database.
     *
     * @param string $userId The ID of the user to delete.
     * @return bool True if the user was deleted successfully, false otherwise.
     */
    public function delete(string $userId): bool
    {
        try {
            // Begin transaction
            self::$pdo->beginTransaction();

            $query = self::$pdo->prepare(
                'DELETE FROM `USER` WHERE id = :userId'
            );
            $query->bindParam(':userId', $userId);
            $query->execute();
            $query->closeCursor();

            // Commit transaction
            self::$pdo->commit();
            return true;
        } catch (Throwable $th) {
            // Rollback transaction in case of error
            self::$pdo->rollBack();
            error_log($th->getMessage());
            return false;
        }
    }
}