<?php

namespace MVC;

class Router {

    public $rutasGET = [];
    public $rutasPOST = [];
    public $rutasPUT = [];

    public function get($url, $fn) {
        $this->rutasGET[$url] = $fn;
    }
    public function post($url, $fn) {
        $this->rutasPOST[$url] = $fn;
    }
    public function put($url, $fn) {

        $this->rutasPUT[$url] = $fn;
    }

    public function comprobarRutas() {
        if (!isset($_SESSION)) {
            session_start();
        }
        $auth = $_SESSION["login"] ?? false;

        // Rutas protegidas
        //  $rutas_protegidas = ["/admin", "/noticias/listado", "/noticias/crear", "/noticias/actualizar"];

        $urlActual = strtok($_SERVER["REQUEST_URI"], "?") ?? "/";
        $metodo = $_SERVER["REQUEST_METHOD"];

        if ($metodo === "GET") {
            $fn = $this->rutasGET[$urlActual] ?? null;

        } else if ($metodo === "POST") {
            $fn = $this->rutasPOST[$urlActual] ?? null;

        } else if ($metodo === "PUT") {
            foreach ($this->rutasPUT as $ruta => $fn) {
                
                $rutaRegex = preg_replace('/:\w+/', '(\d+)', $ruta);
                
                if (preg_match("#^$rutaRegex$#", $urlActual, $matches)) {
                
                    return call_user_func_array($fn, array_slice($matches, 1));
                }
            }
        }

        if ($fn) {
            call_user_func($fn, $this);
        } else {
            echo "Página no encontrada";
            //$this->render("layout", "paginas/error");
        }
    }



    public function render($layout, $view, $datos = []) {

        foreach ($datos as $key => $value) {
            $$key = $value;
        }
        ob_start();

        include __DIR__ . "/views/$view.php";

        $contenido = ob_get_clean();

        include __DIR__ . "/views/$layout.php";
    }
}
