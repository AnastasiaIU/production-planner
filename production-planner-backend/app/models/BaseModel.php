<?php

namespace App\Models;

use App\Services\ResponseService;
use PDO;
use PDOException;

/**
 * Handles the database connection using PDO.
 */
abstract class BaseModel
{
    protected static ?PDO $pdo = null;

    function __construct()
    {
        if (!self::$pdo) {

            $host = $_ENV['DB_HOST'];
            $db = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASSWORD'];
            $charset = $_ENV['DB_CHARSET'];

            try {
                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ];

                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                error_log($e->getMessage());
                ResponseService::Error($e->getMessage());
            }
        }
    }

    /**
     * Checks if there are any records in the specified table.
     *
     * @param string $tableName The name of the table to check.
     * @return bool True if there are records, false otherwise.
     */
    protected function hasAnyRecordsInTable(string $tableName): bool
    {
        $query = self::$pdo->query("SELECT IF(EXISTS(SELECT 1 FROM `$tableName`), 1, 0) AS result");
        $result = $query->fetch();
        return (bool)$result['result'];
    }
}
