<?php

namespace core;

use Exception;
use tools\Utils;
use ReflectionMethod;

/**
 * Class Router
 *
 * Responsible for routing requests to the appropriate controller and action.
 */
class Router
{
    private string $defaultController = "User";
    private string $defaultAction = "index";

    /**
     * Runs the router to handle the current request.
     *
     * Parses the URL, sanitizes the controller and action names, and calls the
     * corresponding action of the controller.
     */
    public function run(): void
    {
        try {
            $url = $this->parseUrl();
            $controllerName = $this->sanitizeControllerName($url["controller"]);
            $action = $this->sanitizeActionName($url["action"]);
            $param = $url["param"];

            $controllerNamespace = "controllers\\";
            $controllerClass = $controllerNamespace . $controllerName;

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();

                if ($this->isValidAction($controller, $action)) {
                    $this->callAction($controller, $action, $param);
                } else {
                    Utils::jsonResponse("Método inválido: $action em $controllerName", 400);
                }
            } else {
                Utils::jsonResponse("Controlador inválido: $controllerName", 400);
            }
        } catch (Exception $e) {
            Utils::debug_log(["coreErrorRouter-run" => $e->getMessage()], "error");
            Utils::jsonResponse(["error" => "Internal server error"], 500);
        }
    }

    /**
     * Parses the URL to extract the controller, action, and parameter.
     *
     * @return array An array containing 'controller', 'action', and 'param' values.
     */
    private function parseUrl(): array
    {
        try {
            $url = isset($_GET["url"]) ? explode("/", rtrim($_GET["url"], "/")) : [];
            return [
                "controller" => $url[0] ?? $this->defaultController,
                "action" => $url[1] ?? $this->defaultAction,
                "param" => $url[2] ?? null,
            ];
        } catch (Exception $e) {
            Utils::debug_log(["coreErrorRouter-parseUrl" => $e->getMessage()], "error");
            return ["controller" => $this->defaultController, "action" => $this->defaultAction, "param" => null];
        }
    }

    /**
     * Sanitizes the controller name to ensure it is valid.
     *
     * @param string $name The name of the controller.
     * @return string The sanitized controller name.
     */
    private function sanitizeControllerName(string $name): string
    {
        try {
            return preg_replace("/[^a-zA-Z0-9]/", "", ucfirst($name)) . "Controller";
        } catch (Exception $e) {
            Utils::debug_log(["coreErrorRouter-sanitizeControllerName" => $e->getMessage()], "error");
            return $this->defaultController . "Controller";
        }
    }

    /**
     * Sanitizes the action name to ensure it is valid.
     *
     * @param string $name The name of the action.
     * @return string The sanitized action name.
     */
    private function sanitizeActionName(string $name): string
    {
        try {
            return preg_replace("/[^a-zA-Z0-9_]/", "", $name);
        } catch (Exception $e) {
            Utils::debug_log(["coreErrorRouter-sanitizeActionName" => $e->getMessage()], "error");
            return $this->defaultAction;
        }
    }

    /**
     * Checks if the action is valid (exists and is public).
     *
     * @param object $controller The controller object.
     * @param string $action The action name.
     * @return bool True if the action is valid, false otherwise.
     */
    private function isValidAction(object $controller, string $action): bool
    {
        try {
            $reflection = new ReflectionMethod($controller, $action);
            return $reflection->isPublic() && !$reflection->isConstructor();
        } catch (Exception $e) {
            Utils::debug_log(["coreErrorRouter-isValidAction" => $e->getMessage()], "error");
            return false;
        }
    }

    /**
     * Calls the specified action of the controller, passing the parameter if needed.
     *
     * @param object $controller The controller object.
     * @param string $action The action name.
     * @param mixed $param The parameter to pass to the action (optional).
     */
    private function callAction(object $controller, string $action, mixed $param = null): void
    {
        try {
            $param ? $controller->$action($param) : $controller->$action();
        } catch (Exception $e) {
            Utils::debug_log(["coreErrorRouter-callAction" => $e->getMessage()], "error");
            Utils::jsonResponse("Erro ao executar ação: " . $e->getMessage(), 500);
        }
    }
}
