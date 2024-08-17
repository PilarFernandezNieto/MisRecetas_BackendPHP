<?php

namespace Controllers;

use Model\Ingrediente;


class IngredienteController {

    public static function index() {
        $ingredientes = Ingrediente::all();
        echo json_encode($ingredientes);
    }

    public static function crear() {
        $ingrediente = new Ingrediente();
        $alertas = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $ingrediente->sincronizar($_POST);

            $alertas = $ingrediente->validar();
            $resultado = "";

            if (empty($alertas)) {
                $resultado = $ingrediente->guardar();
                echo json_encode(["resultado" => $resultado]);
            } else {
                echo json_encode(["alertas" => $alertas]);
            }
        }
    }

    
    public static function actualizar($id) {
        if ($_SERVER["REQUEST_METHOD"] === "PUT") {
            $putData = file_get_contents("php://input");
            $data = json_decode($putData, true);
            $ingrediente = Ingrediente::find($id);
            $ingrediente->sincronizar($data);
      
            $alertas = $ingrediente->validar();
            
            if (empty($alertas)) {
                $resultado = $ingrediente->guardar();
                echo json_encode(["resultado" => $resultado]);
            } else {
                echo json_encode(["alertas" => $alertas]);
            }
        }

    }
    public static function eliminar($id) {

        if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
  
            $ingrediente = Ingrediente::find($id);
            if(!$ingrediente){
                echo json_encode(["resultado" => "No se ha encontrado el ingrediente"]);
            } else {
                try {
                    $resultado = $ingrediente->eliminar();
                    echo json_encode(["resultado" => $resultado]);
                } catch(\Exception $error) {
                     echo json_encode(["error" => "Error al eliminar el ingrediente. Comprueba que no forme parte de alguna receta"]);
                }
            }

          
            
        }
    }
}
