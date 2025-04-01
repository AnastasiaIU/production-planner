<?php

namespace App\Models;

use App\DTO\UserDTO;
use PDO;

/**
 * User class extends Base to interact with the USER entity in the database.
 */
class UserModel extends BaseModel
{
    /**
     * Retrieves a user by their email.
     *
     * @param string $email The email of the user to retrieve.
     * @return UserDTO|null The data transfer object representing the user or null if the user is not found.
     */
    public function getUser(string $email): ?UserDTO
    {
        $query = self::$pdo->prepare(
            'SELECT id, email, password
                    FROM USER
                    WHERE email = :email'
        );
        $query->execute([':email' => $email]);
        $item = $query->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return null;
        }

        return UserDTO::fromArray($item);
    }

    /**
     * Creates a new user in the database.
     *
     * @param string $email The email of the user to create.
     * @param string $password The password of the user to create.
     */
    public function createUser(string $email, string $password): void {
        $query = self::$pdo->prepare('INSERT INTO USER (email, password) VALUES (:email, :password)');

        $query->bindParam(":email", $email);
        $query->bindParam(":password", $password);

        $query->execute();
        $query->closeCursor();
    }

    public function findByEmail($email)
    {
        $stmt = self::$pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}