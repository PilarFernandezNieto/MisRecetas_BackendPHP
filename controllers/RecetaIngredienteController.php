<?php

namespace Controllers;

use Model\Receta;
use Model\Ingrediente;
use Model\RecetaIngrediente;


class RecetaIngredienteController {

    public static function index() {
        $recetas = RecetaIngrediente::all();
        echo json_encode($recetas);
    }

    public static function crear() {
        $alertas = [];
        if(!is_numeric($_GET["id_receta"]) || (!is_numeric($_GET["id_ingrediente"]))) return;
        $ingrediente = new Ingrediente();
        $receta = Receta::find($_GET["id_receta"]);
        $ingredientes = $ingrediente->ingredientesPorReceta($_GET["id_receta"]);
        $alertas = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $receta->sincronizar($_POST);

            $alertas = $receta->validar();
            $resultado = "";

            if (empty($alertas)) {
                $resultado = $receta->guardar();
                echo json_encode(["resultado" => $resultado]);
            } else {
                echo json_encode(["alertas" => $alertas]);
            }

            
        }
    }
    public static function actualizar() {

        $alertas = [];
        if(!is_numeric($_GET["id"])) return;
        $receta = Receta::find($_GET["id"]);


        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $receta->sincronizar($_POST);
            $alertas = $receta->validar();
            if(empty($alertas)){
                $resultado = $receta->guardar();
                echo json_encode(["resultado" => $resultado]);
            } else {
                echo json_encode(["alertas" => $alertas]);
            }
        }

    }
    public static function eliminar() {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $id = $_POST["id"];
            $receta = Receta::find($id);
            $resultado = $receta->eliminar();
            echo json_encode(["resultado" => $resultado]);
        }
    }
}
