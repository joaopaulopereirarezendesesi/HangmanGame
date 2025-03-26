<?php
require_once "config/config.php";
require_once "core/Database.php";
require_once "core/Router.php";
require_once "tools/Utils.php";

class App
{
    public function __construct()
    {
        $this->validateConfig();
        $this->configureCORS();
        $this->initializeRouter();
    }

    function configureCORS(
        array $allowedOrigins = ["http://localhost:5173"],
        array $allowedMethods = ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
        array $allowedHeaders = ["Content-Type", "Authorization"]
    ): void {
        $origin = $_SERVER["HTTP_ORIGIN"] ?? "";

        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true");
        } else {
            header("HTTP/1.1 403 Forbidden");
            exit("Origem não permitida.");
        }

        header(
            "Access-Control-Allow-Methods: " . implode(", ", $allowedMethods)
        );
        header(
            "Access-Control-Allow-Headers: " . implode(", ", $allowedHeaders)
        );

        if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
            $this->handleOptionsRequest($origin);
            exit();
        }
    }

    private function handleOptionsRequest(string $origin): void
    {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("HTTP/1.1 204 No Content");
        exit();
    }

    private function validateConfig(): void
    {
        $requiredConfigs = ["DB_HOST", "DB_USER", "DB_PASS", "DB_NAME"];

        foreach ($requiredConfigs as $config) {
            if (!defined($config)) {
                tools\Utils::debug_log(
                    "Configuração faltando: {$config}",
                    "error"
                );
                tools\Utils::jsonResponse(
                    ["error" => "Internal server error"],
                    500
                );
            }
        }
    }

    private function initializeRouter(): void
    {
        try {
            \core\Database::connect();

            $router = new core\Router();
            $router->run();
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    private function handleException(\Exception $e): void
    {
        tools\Utils::debug_log("Erro fatal: " . $e->getMessage(), "error");
        tools\Utils::jsonResponse(["error" => "Internal server error"], 500);
    }
}

try {
    new App();
} catch (\Exception $e) {
    tools\Utils::debug_log(
        "Erro ao inicializar a aplicação: " . $e->getMessage(),
        "error"
    );
    tools\Utils::jsonResponse(["error" => "Internal server error"], 500);
}
