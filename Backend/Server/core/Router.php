<?php

namespace core;

use tools\Utils;

/**
 * Class Router
 * 
 * Responsible for routing requests to the appropriate controller and action.
 */
class Router
{
    /**
     * Default controller to use if none is provided in the URL.
     *
     * @var string
     */
    private $defaultController = 'User';

    /**
     * Default action to use if none is provided in the URL.
     *
     * @var string
     */
    private $defaultAction = 'index';

    /**
     * Runs the router to handle the current request.
     * 
     * Parses the URL, sanitizes the controller and action names, and calls the
     * corresponding action of the controller.
     */
    public function run()
    {
        $url = $this->parseUrl();

        $controllerName = $this->sanitizeControllerName($url['controller']);
        $action = $this->sanitizeActionName($url['action']);
        $param = $url['param'];

        $controllerNamespace = 'controllers\\';
        $controllerClass = $controllerNamespace . $controllerName;

        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();

            if ($this->isValidAction($controller, $action)) {
                $this->callAction($controller, $action, $param);
            } else {
                Utils::jsonResponse("Método inválido: {$action} em {$controllerName}", 400);
            }
        } else {
            Utils::jsonResponse("Controlador inválido: {$controllerName}", 400);
        }
    }

    /**
     * Parses the URL to extract the controller, action, and parameter.
     * 
     * @return array An array containing 'controller', 'action', and 'param' values.
     */
    private function parseUrl(): array
    {
        $url = isset($_GET['url']) ? explode('/', rtrim($_GET['url'], '/')) : [];
        return [
            'controller' => $url[0] ?? $this->defaultController,
            'action' => $url[1] ?? $this->defaultAction,
            'param' => $url[2] ?? null,
        ];
    }

    /**
     * Sanitizes the controller name to ensure it is valid.
     * 
     * @param string $name The name of the controller.
     * 
     * @return string The sanitized controller name.
     */
    private function sanitizeControllerName($name): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', ucfirst($name)) . 'Controller';
    }

    /**
     * Sanitizes the action name to ensure it is valid.
     * 
     * @param string $name The name of the action.
     * 
     * @return string The sanitized action name.
     */
    private function sanitizeActionName($name): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $name);
    }

    /**
     * Checks if the action is valid (exists and is public).
     * 
     * @param object $controller The controller object.
     * @param string $action The action name.
     * 
     * @return bool True if the action is valid, false otherwise.
     */
    private function isValidAction($controller, string $action): bool
    {
        $reflection = new \ReflectionMethod($controller, $action);
        return $reflection->isPublic() && !$reflection->isConstructor();
    }

    /**
     * Calls the specified action of the controller, passing the parameter if needed.
     * 
     * @param object $controller The controller object.
     * @param string $action The action name.
     * @param mixed $param The parameter to pass to the action (optional).
     */
    private function callAction($controller, string $action, $param = null): void
    {
        try {
            $param ? $controller->$action($param) : $controller->$action();
        } catch (\Exception $e) {
            Utils::jsonResponse('Erro ao executar ação: ' . $e->getMessage(), 500);
        }
    }
}
