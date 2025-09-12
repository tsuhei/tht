<?php

namespace App\Core;

class App
{
    public function __construct()
    {
        // 1) Cargar rutas
        $routes = require __DIR__ . '/../config/routes.php';

        // 2) Detectar URI y método HTTP
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath   = str_replace('index.php', '', $scriptName);

        if (strpos($requestUri, $basePath) === 0) {
            $path = substr($requestUri, strlen($basePath));
        } else {
            $path = $requestUri;
        }
        $path   = '/' . trim($path, '/');
        $method = $_SERVER['REQUEST_METHOD'];

        // 3) Buscar coincidencia en las rutas definidas
        $methodRoutes = $routes[$method] ?? [];
        foreach ($methodRoutes as $pattern => $handler) {
            $regex = "#^{$pattern}$#";
            if (! preg_match($regex, $path, $matches)) {
                continue;
            }
            array_shift($matches);

            // 4) Resolver controlador y acción
            if (is_string($handler)) {
                list($ctrl, $action) = explode('@', $handler);
            } elseif (is_array($handler)) {
                list($ctrl, $action) = $handler;
            } else {
                throw new \Exception("Handler inválido para ruta {$pattern}");
            }

            // 4a) Normalizar barras a namespace
            $ctrl = str_replace(['/', '\\'], '\\', trim($ctrl, '\\'));

            // 4b) Añadir sufijo "Controller" si no existe
            if (substr($ctrl, -10) !== 'Controller') {
                $ctrl .= 'Controller';
            }

            // 4c) Construir la clase completa
            $controllerClass = "App\\Controllers\\{$ctrl}";
            if (! class_exists($controllerClass)) {
                throw new \Exception("Clase {$controllerClass} no encontrada");
            }

            // 5) Instanciar y llamar al método
            $controller = new $controllerClass();
            if (! method_exists($controller, $action)) {
                throw new \Exception("Método {$action} no existe en {$controllerClass}");
            }

            call_user_func_array([$controller, $action], $matches);
            return;
        }

        // 6) Si ninguna ruta coincide → 404
        http_response_code(404);
        echo '404 | Página no encontrada';
    }

    public function run(): void
    {
        // El dispatch se ejecuta en el constructor,
        // este método puede quedarse vacío o usarse para hooks futuros.
    }
}
