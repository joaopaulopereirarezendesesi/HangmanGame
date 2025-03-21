<?php

namespace core; // Define o namespace da classe como "core"

class Router
{
    // Define o controlador e ação padrão caso a URL não forneça esses valores
    private $defaultController = 'User';
    private $defaultAction = 'index';

    // Método principal que gerencia o fluxo de roteamento
    public function run()
    {
        // Analisa a URL da requisição e extrai controlador, ação e parâmetros
        $url = $this->parseUrl();

        // Sanitiza os nomes do controlador e da ação para garantir segurança
        $controllerName = $this->sanitizeControllerName($url['controller']);
        $action = $this->sanitizeActionName($url['action']);
        $param = $url['param'];

        // Define o namespace onde os controladores estão localizados
        $controllerNamespace = 'controllers\\';
        // Concatena o nome do controlador com o namespace
        $controllerClass = $controllerNamespace . $controllerName;

        // Verifica se a classe do controlador existe
        if (class_exists($controllerClass)) {
            // Instancia o controlador
            $controller = new $controllerClass();

            // Verifica se a ação é válida
            if ($this->isValidAction($controller, $action)) {
                // Chama a ação no controlador com o parâmetro (se houver)
                $this->callAction($controller, $action, $param);
            } else {
                // Loga a tentativa inválida e envia um erro 404
                $this->logAttempt("Método inválido: {$action} em {$controllerName}");
                $this->sendError('Método não encontrado', 404);
            }
        } else {
            // Loga a tentativa de acessar um controlador inválido e envia um erro 404
            $this->logAttempt("Classe inválida: {$controllerClass}");
            $this->sendError('Classe não encontrada', 404);
        }
    }

    // Método que analisa a URL da requisição e retorna um array com o controlador, ação e parâmetros
    private function parseUrl(): array
    {
        // Obtém a URL da requisição e divide-a em partes
        $url = isset($_GET['url']) ? explode('/', rtrim($_GET['url'], '/')) : [];
        return [
            'controller' => $url[0] ?? $this->defaultController, // Controlador padrão
            'action' => $url[1] ?? $this->defaultAction, // Ação padrão
            'param' => $url[2] ?? null, // Parâmetro opcional
        ];
    }

    // Método que sanitiza o nome do controlador, garantindo que ele seja válido
    private function sanitizeControllerName($name): string
    {
        // Remove caracteres inválidos e garante que a primeira letra seja maiúscula, além de adicionar "Controller" ao final
        return preg_replace('/[^a-zA-Z0-9]/', '', ucfirst($name)) . 'Controller';
    }

    // Método que sanitiza o nome da ação, removendo caracteres inválidos
    private function sanitizeActionName($name): string
    {
        // Remove caracteres que não sejam alfanuméricos ou underscores
        return preg_replace('/[^a-zA-Z0-9_]/', '', $name);
    }

    // Método que verifica se a ação é válida (existe e é pública)
    private function isValidAction($controller, string $action): bool
    {
        // Usa ReflectionMethod para verificar se o método é público e não é o construtor
        $reflection = new \ReflectionMethod($controller, $action);
        return $reflection->isPublic() && !$reflection->isConstructor();
    }

    // Método que chama a ação no controlador com o parâmetro, se houver
    private function callAction($controller, string $action, $param = null): void
    {
        try {
            // Se houver parâmetro, chama a ação com o parâmetro
            $param ? $controller->$action($param) : $controller->$action();
        } catch (\Exception $e) {
            // Caso ocorra um erro, envia uma resposta de erro 500
            $this->sendError('Erro ao executar ação: ' . $e->getMessage(), 500);
        }
    }

    // Método que envia uma resposta de erro com uma mensagem e código HTTP
    private function sendError(string $message, int $code): void
    {
        http_response_code($code); // Define o código de resposta HTTP
        echo json_encode(['error' => $message]); // Envia a mensagem de erro no formato JSON
        exit; // Encerra a execução do script
    }

    // Método que loga tentativas inválidas
    private function logAttempt($message): void
    {
        error_log("Tentativa inválida: {$message}"); // Registra a tentativa no log de erros
    }
}
