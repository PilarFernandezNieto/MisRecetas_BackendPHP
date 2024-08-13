<?php

namespace MVC;
class Router {

    public $rutasGET = [];
    public $rutasPOST = [];

    public function get($url, $fn){
        $this->rutasGET[$url] = $fn;
       
    }
    public function post($url, $fn) {
        $this->rutasPOST[$url] = $fn;
    }

    public function comprobarRutas(){
        if(!isset($_SESSION)){
            session_start();
        }
        $auth = $_SESSION["login"] ?? false;

        // Rutas protegidas
        //  $rutas_protegidas = ["/admin", "/noticias/listado", "/noticias/crear", "/noticias/actualizar"];

        //$urlActual = $_SERVER["PATH_INFO"] ?? "/";
        $urlActual = strtok($_SERVER["REQUEST_URI"], "?") ?? "/";
        $metodo = $_SERVER["REQUEST_METHOD"];

      

        if ($metodo === "GET"){
            $fn = $this->rutasGET[$urlActual] ?? null;
        } else {

            $fn = $this->rutasPOST[$urlActual] ?? null;
        } 

        // if(in_array($urlActual, $rutas_protegidas) && !$auth){
        //    header("Location: /");
            
        // }

        if($fn){
            call_user_func($fn, $this);
        } else {
            //echo "Página no encontrada";
            $this->render("layout", "paginas/error");
        }
    }

    public function render($layout, $view, $datos=[]){

      foreach($datos as $key=>$value){
        $$key = $value;
      }
        ob_start();

        include __DIR__ . "/views/$view.php";

        $contenido = ob_get_clean();

        include __DIR__ . "/views/$layout.php";

    }


}