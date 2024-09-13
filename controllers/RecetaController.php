<?php

namespace Controllers;

use Model\Receta;
use Model\Ingrediente;
use Model\RecetaIngrediente;



class RecetaController {

    public static function index() {
        $recetas = Receta::all();
        $recetasCompletas = [];


        foreach ($recetas as $receta) {
            $ingredientes = Ingrediente::ingredientesPorReceta($receta->id);
            $recetaCompleta = [
                "receta" => $receta,
                "ingredientes" => $ingredientes
            ];
            $recetasCompletas[] = $recetaCompleta;
        }

        echo json_encode($recetasCompletas);
    }

    public static function getReceta(){
        $receta =[];
        if(isset($_GET["nombre"])){
            $nombre = s($_GET["nombre"]);
            $receta = Receta::getByName($nombre);
        } else if(isset($_GET["id"])){
            if(!is_numeric($_GET["id"])) return;
            $receta = Receta::find($_GET["id"]);


        }
        if($receta){
            $ingredientes = Ingrediente::ingredientesPorReceta($receta->id);
            $recetaCompleta = [
                "receta" => $receta,
                "ingredientes" => $ingredientes
            ];
            echo json_encode($recetaCompleta);
        } else{
            echo json_encode([
                'resultado' => 'error',
                'mensaje' => 'No hay datos'
            ]);
        }

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


                if(!empty($_FILES["imagen"]["tmp_name"])){
                    if (!is_dir(CARPETA_IMAGENES)) {
                        mkdir(CARPETA_IMAGENES);
                    }
                    echo CARPETA_IMAGENES;
                    $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
                    move_uploaded_file($_FILES['imagen']['tmp_name'], CARPETA_IMAGENES . $nombreImagen);
                    $receta->imagen = $nombreImagen;
                }

                $resultado = $receta->guardar();
                $id_receta = $resultado["id"];
                if ($id_receta) {
                    $ingredientesIds = $_POST["ingrediente_id"] ?? "";
                    $cantidades = $_POST["cantidad"] ?? "";


                   for($i=0; $i<count($ingredientesIds); $i++) {
                    $id_ingrediente = $ingredientesIds[$i];
                    $cantidad = $cantidades[$i];
                        $recetaIngrediente = new RecetaIngrediente([
                            'id_receta' => $id_receta,
                            'id_ingrediente' => $id_ingrediente,
                            "cantidad" => $cantidad
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


    public static function actualizar($id) {

      
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
