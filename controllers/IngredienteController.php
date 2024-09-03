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
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $ingrediente->sincronizar($_POST);
            $alertas = $ingrediente->validar();
            if (empty($alertas)) {
                try {
                    $resultado = $ingrediente->guardar();
                    echo json_encode(
                        [
                            "result" => $resultado,
                            "msg" => "Creado correctamente"
                        ]
                    );
                } catch (\Exception $e) {
                    if ($e->getCode() === 1062) {
                        echo json_encode(
                            [
                                "result" => "error",
                                "msg" => "Ese ingrediente ya existe"
                            ]
                        );
                    } else {
                        echo json_encode(
                            [
                                "result" => "error",
                                "msg" => "Ha ocurrido un error"
                            ]
                        );
                    }
                }
            } else {
                echo json_encode(["alertas" => $alertas]);
            }
        }
    }
    public static function getById($id) {
        if (empty($id) || !filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            echo json_encode(["result" => "error", "msg"=> "ID no válido"]);
            http_response_code(400);
            return;
        }
        $ingrediente = Ingrediente::find($id);

        echo json_encode(["result" => "success", "ingrediente" =>$ingrediente]);
    }


    public static function actualizar($id) {
        $resultado = "";
        if ($_SERVER["REQUEST_METHOD"] === "PUT") {
            $putData = file_get_contents("php://input");

            $data = json_decode($putData, true);

            $ingrediente = Ingrediente::find($id);
            if (!$ingrediente) {
                echo json_encode(["result" => "error", "msg" =>"No se ha encontrado el ingrediente"]);
            } else {
                $ingrediente->sincronizar($data);
                $alertas = $ingrediente->validar();
                if (empty($alertas)) {
                    try {
                        $resultado = $ingrediente->guardar();
                        echo json_encode(
                            [
                                "result" => $resultado,
                                "msg" => "Actualizado correctamente",
                                "ingrediente" => $ingrediente
                            ]
                        );

                    } catch (\Exception $e) {
                        if ($e->getCode() === 1062) {
                            echo json_encode(
                                [
                                    "result" => "error",
                                    "msg" => "Ese ingrediente ya existe"
                                ]
                            );
                        } else {
                            echo json_encode(
                                [
                                    "result" => "error",
                                    "msg" => "Ha ocurrido un error"
                                ]
                            );
                        }
                    }

                } else {
                    echo json_encode(["alertas" => $alertas]);
                }
            }

        }
    }
    public static function eliminar($id) {

        if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

            $ingrediente = Ingrediente::find($id);
            if (!$ingrediente) {
                echo json_encode(["result" => "error", "msg" => "No se ha encontrado el ingrediente"]);
            } else {
                try {
                    $resultado = $ingrediente->eliminar();
                    echo json_encode(["result" => "success", "msg" => "Eliminado correctamente", "resultado" => $resultado]);
                } catch (\Exception $error) {
                    echo json_encode(["result" =>"error", "msg" => $error->getMessage()]);
                }
            }
        }
    }
}
