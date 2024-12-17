<?php

namespace core;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../tools/helpers.php';

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $instance = null;

    public static function connect(array $options = []): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = self::getDsn();
                self::$instance = new PDO(
                    $dsn,
                    DB_USER,
                    DB_PASS,
                    self::getDefaultOptions($options)
                );
            } catch (PDOException $e) {
                displayMessage("Erro na conexão: " . $e->getMessage(), 'error');
                throw new PDOException("Erro ao conectar ao banco de dados.");
            }
        }

        return self::$instance;
    }

    private static function getDsn(): string
    {
        return 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    }

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