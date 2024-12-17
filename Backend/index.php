<?php

require_once 'config/config.php';
require_once 'core/Database.php';
require_once 'core/Router.php';

class App
{
    public function __construct()
    {
        //$this->configureCORS();
        $this->validateConfig();
        $this->handleOptionsRequest();
        $this->initializeRouter();
    }

    function configureCORS(
        array $allowedOrigins = ['http://localhost:5500'],
        array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        array $allowedHeaders = ['Content-Type', 'Authorization']
    ): void {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if ($origin === '' && $_SERVER['SERVER_NAME'] === 'localhost') {
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Credentials: true");
        } elseif (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true");
        } else {
            header("HTTP/1.1 403 Forbidden");
            exit(json_encode(['error' => 'Origem não permitida.']));
        }

        header("Access-Control-Allow-Methods: " . implode(', ', $allowedMethods));
        header("Access-Control-Allow-Headers: " . implode(', ', $allowedHeaders));

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header("HTTP/1.1 204 No Content");
            exit();
        }
    }


    private function validateConfig(): void
    {
        $requiredConfigs = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME'];
        foreach ($requiredConfigs as $config) {
            if (!defined($config)) {
                error_log("Configuração faltando: {$config}");
                errorResponse("Configuração faltando no servidor: {$config}", 500);
            }
        }
    }

    private function handleOptionsRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            jsonResponse(['status' => 'OK'], 200);
        }
    }

    private function initializeRouter(): void
    {
        try {
            \core\Database::connect();
            displayMessage("Conexão com o banco de dados estabelecida com sucesso!", 'success');

            $router = new core\Router();
            $router->run();
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    private function handleException(\Exception $e): void
    {
        error_log("Erro fatal: " . $e->getMessage());
        errorResponse("Ocorreu um erro interno no servidor. Tente novamente mais tarde.", 500);
    }
}

try {
    $controlScript = __DIR__ . '/tools/WSserverControl.php';
    $command = "start php \"$controlScript\"";
    pclose(popen($command, "r"));
    new App();
    
} catch (\Exception $e) {
    error_log("Erro ao inicializar a aplicação: " . $e->getMessage());
    errorResponse("Erro crítico ao iniciar a aplicação.", 500);
}
