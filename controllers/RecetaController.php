<?php

namespace Controllers;

use Model\Receta;
use Model\Ingrediente;
use Model\RecetaIngrediente;



class RecetaController {

    // public static function index() {
    //     $recetas = Receta::all();
    //     echo json_encode($recetas);
    // }

    public static function index() {
        $recetas = Receta::all();

        foreach($recetas as $receta){
           $receta->ingredientes = Ingrediente::ingredientesPorReceta($receta->id);
        }
   

        echo json_encode($recetas);
    }

    public static function crear() {
        $receta = new Receta();
        $ingredientes = Ingrediente::all();
        $alertas = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            $receta->sincronizar($_POST);

            $alertas = $receta->validar();
            $resultado = "";

            if (empty($alertas)) {
                $resultado = $receta->guardar();
                $id_receta = $resultado["id"];
                if($id_receta){
                    $ingredientes = $_POST['ingredientes'] ?? [];

                    foreach ($ingredientes as $id_ingrediente) {
                        $recetaIngrediente = new RecetaIngrediente([
                            'id_receta' => $id_receta,
                            'id_ingrediente' => $id_ingrediente
                        ]);
                        $recetaIngrediente->sincronizar();
                        $recetaIngrediente->guardar();
                    }
                }
                echo json_encode([
                    'resultado' => 'success',
                    'mensaje' => 'Receta creada correctamente'
                ]);
            } else {
                echo json_encode([
                    'resultado' => 'error',
                    'mensaje' => 'Hubo un error al crear la receta'
                ]);
            }

            
        }
    }


    public static function actualizar() {

        $alertas = [];
        if(!is_numeric($_GET["id"])) return;
        $receta = Receta::find($_GET["id"]);
        $ingredientes = Ingrediente::ingredientesPorReceta($receta->id);
       

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            
            $receta->sincronizar($_POST);
           

            foreach ($ingredientes as $id_ingrediente) {
                $recetaIngrediente = new RecetaIngrediente([
                    'id_receta' => $receta->id,
                    'id_ingrediente' => $id_ingrediente
                ]);
                $recetaIngrediente->sincronizar();
                $recetaIngrediente->guardar();
            }
           
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
