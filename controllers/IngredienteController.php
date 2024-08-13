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
    public static function actualizar() {

        $alertas = [];
        if(!is_numeric($_GET["id"])) return;
        $ingrediente = Ingrediente::find($_GET["id"]);


        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $ingrediente->sincronizar($_POST);
            $alertas = $ingrediente->validar();
            if(empty($alertas)){
                $resultado = $ingrediente->guardar();
                echo json_encode(["resultado" => $resultado]);
            } else {
                echo json_encode(["alertas" => $alertas]);
            }
        }

    }
    public static function eliminar() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $id = $_POST["id"];
            $ingrediente = Ingrediente::find($id);
            $resultado = $ingrediente->eliminar();
            echo json_encode(["resultado" => $resultado]);
        }
    }
}
