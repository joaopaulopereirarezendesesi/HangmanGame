<?php

// Carrega arquivos essenciais de configuração, banco de dados, roteamento e utilitários
require_once 'config/config.php';
require_once 'core/Database.php';
require_once 'core/Router.php';
require_once 'tools/Utils.php';

class App
{
    public function __construct()
    {
        // Configura permissões de CORS (Cross-Origin Resource Sharing)
        $this->configureCORS();

        // Valida se todas as configurações obrigatórias estão definidas
        $this->validateConfig();

        // Trata requisições do tipo OPTIONS para CORS
        $this->handleOptionsRequest();

        // Inicializa o roteador da aplicação
        $this->initializeRouter();
    }

    function configureCORS(
        array $allowedOrigins = ['http://localhost:5173'], // Origens permitidas
        array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'], // Métodos HTTP permitidos
        array $allowedHeaders = ['Content-Type', 'Authorization'] // Cabeçalhos permitidos
    ): void {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        // Verifica se a origem da requisição está na lista de permitidos
        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true");
        } else {
            // Caso a origem não seja permitida, retorna um erro 403 (Forbidden)
            header("HTTP/1.1 403 Forbidden");
            exit('Origem não permitida.');
        }

        // Define os métodos e cabeçalhos permitidos
        header("Access-Control-Allow-Methods: " . implode(', ', $allowedMethods));
        header("Access-Control-Allow-Headers: " . implode(', ', $allowedHeaders));

        // Trata requisições OPTIONS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $this->handleOptionsRequest();
            exit();
        }
    }

    private function handleOptionsRequest(): void
    {
        // Caso a requisição seja do tipo OPTIONS, define os cabeçalhos e encerra
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            header("HTTP/1.1 204 No Content");
            exit();
        }
    }

    private function validateConfig(): void
    {
        // Lista de configurações obrigatórias
        $requiredConfigs = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME'];

        // Verifica se cada configuração está definida, caso contrário, retorna erro
        foreach ($requiredConfigs as $config) {
            if (!defined($config)) {
                tools\Utils::displayMessage("Configuração faltando: {$config}", 'error');
                tools\Utils::errorResponse("Configuração faltando no servidor: {$config}", 500);
            }
        }
    }

    private function initializeRouter(): void
    {
        try {
            // Estabelece conexão com o banco de dados
            \core\Database::connect();

            // Cria uma instância do roteador e executa o roteamento
            $router = new core\Router();
            $router->run();
        } catch (\Exception $e) {
            // Em caso de erro, trata a exceção
            $this->handleException($e);
        }
    }

    private function handleException(\Exception $e): void
    {
        // Exibe mensagem de erro e retorna resposta de erro ao cliente
        tools\Utils::displayMessage("Erro fatal: " . $e->getMessage(), 'error');
        tools\Utils::errorResponse("Ocorreu um erro interno no servidor. Tente novamente mais tarde.", 500);
    }
}

try {
    // Inicializa a aplicação
    new App();
} catch (\Exception $e) {
    // Captura qualquer erro na inicialização da aplicação e responde com erro 500
    tools\Utils::displayMessage("Erro ao inicializar a aplicação: " . $e->getMessage(), 'error');
    tools\Utils::errorResponse("Erro crítico ao iniciar a aplicação.", 500);
}
