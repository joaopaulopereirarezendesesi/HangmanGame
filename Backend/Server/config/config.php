<?php

// Inclui o autoload do Composer para carregar automaticamente as classes do projeto
require_once __DIR__ . '/../vendor/autoload.php';

// Importa a classe Dotenv do pacote vlucas/phpdotenv
use Dotenv\Dotenv;

// Cria uma instância do objeto Dotenv, indicando que o arquivo .env está localizado na pasta anterior (__DIR__ . '/../')
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');

// Carrega as variáveis de ambiente do arquivo .env para o ambiente de execução do PHP
$dotenv->load();

// Define constantes para os valores de configuração do banco de dados e do JWT, usando as variáveis de ambiente carregadas
// Essas constantes são usadas para configurar a conexão com o banco de dados e o segredo do JWT
define('DB_HOST', $_ENV['DB_HOST']); // Endereço do servidor do banco de dados
define('DB_NAME', $_ENV['DB_NAME']); // Nome do banco de dados
define('DB_USER', $_ENV['DB_USER']); // Nome de usuário do banco de dados
define('DB_PASS', $_ENV['DB_PASS']); // Senha do banco de dados
define('JWT_SECRET', $_ENV['JWT_SECRET']); // Chave secreta usada para gerar e validar JWTs
