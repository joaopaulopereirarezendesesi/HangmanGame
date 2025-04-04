<?php

namespace core;

require_once __DIR__ . "/../config/config.php";

use tools\Utils;
use PDO;
use PDOException;

final class Database
{
    private static ?PDO $instance = null;

    /**
     * Connects to the database using PDO.
     *
     * @param array $options Custom options for PDO configuration.
     * @return PDO The database connection instance.
     * @throws PDOException If there is an error connecting to the database.
     */
    public static function connect(array $options = []): PDO
    {
        if (self::$instance === null) {
            try {
                $dataSourceName = self::getDataSourceName();

                self::$instance = new PDO(
                    $dataSourceName,
                    DB_USER,
                    DB_PASS,
                    self::getDefaultOptions($options)
                );
            } catch (PDOException $e) {
                Utils::debug_log(
                    "Connection error: " . $e->getMessage(),
                    "error"
                );
                throw new PDOException("Error connecting to the database.");
            }
        }

        return self::$instance;
    }

    /**
     * Generates the Data Source Name (DSN) for the PDO connection to the database.
     *
     * @return string The DSN for the database connection.
     */
    private static function getDataSourceName(): string
    {
        return "mysql:host=" .
            DB_HOST .
            ";dbname=" .
            DB_NAME .
            ";charset=utf8mb4";
    }

    /**
     * Gets the default options for PDO configuration.
     *
     * @param array $customOptions Custom options to merge with the default options.
     * @return array The PDO configuration options.
     */
    private static function getDefaultOptions(array $customOptions = []): array
    {
        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return array_replace($defaultOptions, $customOptions);
    }
}