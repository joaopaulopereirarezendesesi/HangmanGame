<?php
class Router {
    public function run() {
        $url = isset($_GET['url']) ? explode('/', rtrim($_GET['url'], '/')) : ['user', 'index'];
        $controllerName = ucfirst($url[0]) . 'Controller';
        $action = $url[1] ?? 'index';
        $param = $url[2] ?? null;

        $controllerPath = '../app/controllers/' . $controllerName . '.php';

        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $controller = new $controllerName();

            if (method_exists($controller, $action)) {
                if ($param) {
                    $controller->$action($param);
                } else {
                    $controller->$action();
                }
            } else {
                echo json_encode(['error' => 'Método não encontrado']);
            }
        } else {
            echo json_encode(['error' => 'Controlador não encontrado']);
        }
    }
}
