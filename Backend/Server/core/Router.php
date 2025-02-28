<?php

namespace core;

class Router
{
    private $defaultController = 'User';
    private $defaultAction = 'index';

    public function run()
    {
        $url = $this->parseUrl();

        $controllerName = $this->sanitizeControllerName($url['controller']);
        $action = $this->sanitizeActionName($url['action']);
        $param = $url['param'];

        $controllerPath = realpath(__DIR__ . "/../controllers/{$controllerName}.php");

        if ($this->isValidController($controllerPath)) {
            require_once $controllerPath;

            $controller = new $controllerName();

            if ($this->isValidAction($controller, $action)) {
                $this->callAction($controller, $action, $param);
            } else {
                $this->logAttempt("Método inválido: {$action} em {$controllerName}");
                $this->sendError('Método não encontrado', 404);
            }
        } else {
            $this->logAttempt("Controlador inválido: {$controllerName}");
            $this->sendError('Controlador não encontrado', 404);
        }
    }

    private function parseUrl(): array
    {
        $url = isset($_GET['url']) ? explode('/', rtrim($_GET['url'], '/')) : [];
        return [
            'controller' => $url[0] ?? $this->defaultController,
            'action'     => $url[1] ?? $this->defaultAction,
            'param'      => $url[2] ?? null,
        ];
    }

    private function sanitizeControllerName($name): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', ucfirst($name)) . 'Controller';
    }

    private function sanitizeActionName($name): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $name);
    }

    private function isValidController($path): bool
    {
        return $path && strpos($path, realpath(__DIR__ . '/../controllers')) === 0;
    }

    private function isValidAction($controller, string $action): bool
    {
        $reflection = new \ReflectionMethod($controller, $action);
        return $reflection->isPublic() && !$reflection->isConstructor();
    }

    private function callAction($controller, string $action, $param = null): void
    {
        try {
            $param ? $controller->$action($param) : $controller->$action();
        } catch (\Exception $e) {
            $this->sendError('Erro ao executar ação: ' . $e->getMessage(), 500);
        }
    }

    private function sendError(string $message, int $code): void
    {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }

    private function logAttempt($message): void
    {
        error_log("Tentativa inválida: {$message}");
    }
}
