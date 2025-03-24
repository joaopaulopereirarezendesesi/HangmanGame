<?php

namespace core;

require_once __DIR__ . '/../config/config.php';

use tools\Utils;
use PDO;
use PDOException;

final class Database
{
    private static ?PDO $instance = null;

    /**
     * Conecta ao banco de dados usando PDO.
     *
     * @param array $options Opções customizadas para a configuração do PDO.
     * @return PDO A instância de conexão com o banco de dados.
     * @throws PDOException Se houver erro ao conectar ao banco de dados.
     */
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
                Utils::displayMessage("Erro na conexão: " . $e->getMessage(), 'error');
                throw new PDOException("Erro ao conectar ao banco de dados.");
            }
        }

        return self::$instance;
    }

    /**
     * Gera o Data Source Name (DSN) para a conexão PDO com o banco de dados.
     *
     * @return string O DSN para a conexão com o banco de dados.
     */
    private static function getDsn(): string
    {
        return 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    }

    /**
     * Obtém as opções padrão para a configuração do PDO.
     *
     * @param array $customOptions Opções customizadas para mesclar com as opções padrão.
     * @return array As opções de configuração do PDO.
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
