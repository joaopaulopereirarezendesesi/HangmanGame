<?php

namespace core; // Define o namespace como "core", o que indica que a classe Database faz parte do núcleo do sistema

// Inclui a configuração global do projeto, que provavelmente define constantes como DB_HOST, DB_USER, etc.
require_once __DIR__ . '/../config/config.php';

use tools\Utils; // Importa a classe Utils, que parece ser usada para exibir mensagens de erro
use PDO; // Importa a classe PDO, que permite a comunicação com o banco de dados
use PDOException; // Importa a classe PDOException, que trata exceções relacionadas ao PDO

final class Database
{
    // Atributo estático para armazenar a instância única da conexão PDO
    private static ?PDO $instance = null;

    // Método público e estático para realizar a conexão com o banco de dados
    public static function connect(array $options = []): PDO
    {
        // Verifica se já existe uma instância da conexão. Se não, cria uma nova.
        if (self::$instance === null) {
            try {
                // Obtém o DSN (Data Source Name) para conectar ao banco de dados
                $dsn = self::getDsn();

                // Cria uma nova instância de PDO com as credenciais do banco de dados
                self::$instance = new PDO(
                    $dsn,
                    DB_USER, // Usuário do banco de dados, vindo de uma constante configurada no arquivo .env
                    DB_PASS, // Senha do banco de dados, também configurada no arquivo .env
                    self::getDefaultOptions($options) // Opções de configuração adicionais para o PDO
                );
            } catch (PDOException $e) {
                // Se ocorrer um erro na conexão, exibe uma mensagem de erro e lança uma exceção personalizada
                Utils::displayMessage("Erro na conexão: " . $e->getMessage(), 'error');
                throw new PDOException("Erro ao conectar ao banco de dados.");
            }
        }

        // Retorna a instância única do PDO
        return self::$instance;
    }

    // Método privado para gerar o DSN (Data Source Name) necessário para a conexão PDO
    private static function getDsn(): string
    {
        // Cria e retorna a string do DSN para conexão com um banco de dados MySQL
        return 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    }

    // Método privado para obter as opções padrão de configuração do PDO, podendo mesclar com opções customizadas
    private static function getDefaultOptions(array $customOptions = []): array
    {
        // Opções padrão para configuração do PDO
        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Ativa o modo de erro para exceções
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Define o modo de busca padrão como ARRAY associativo
            PDO::ATTR_EMULATE_PREPARES => false, // Desativa a emulação de instruções preparadas, para usar as reais do MySQL
        ];

        // Mescla as opções customizadas (se passadas) com as opções padrão
        return array_replace($defaultOptions, $customOptions);
    }
}
