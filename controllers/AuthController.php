<?php

namespace Controllers;

use Clases\Email;
use Model\Usuario;

class AuthController
{


    public static function login(): void
    {
        $resultado = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            $alertas = $auth->validarPassword();

            if (empty($alertas)) {
                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);
                if ($usuario) {
                    // Verificar el password
                    if ($usuario->comprobarPassword($auth->password)) {
                        // Autenticar el usuario
                        if (!isset($_SESSION)) {
                            session_start();
                        }

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellidos;
                        $_SESSION['email'] = $usuario->email;

                    } else {
                        $resultado =
                            [
                                "result" => "error",
                                "msg" => Usuario::getAlertas()
                            ];

                    }

                } else {
                    $resultado =
                        [
                            "result" => "error",
                            "msg" => "Usuario no encontrado"
                        ];


                }

            } else {
                $resultado =
                    [
                        "result" => "error",
                        "msg" => $alertas
                    ];

            }
            echo json_encode($resultado);
        }




    }

    public static function registro(): void
    {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Cuando los datos se envían en json desde postman
            // // Leer el contenido JSON del cuerpo de la solicitud
            // $data = file_get_contents("php://input");
            // // Decodificar el JSON en un array asociativo
            // $json_data = json_decode($data, true);

            // Para recibirlos en post, debemos enviar desde postman un content-type application/x-www-form-urlencoded

            $usuario = new Usuario();
            $alertas = [];

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $usuario->sincronizar($_POST);

                $alertas = $usuario->validarNuevaCuenta();

            }
            if (empty($alertas)) {

                $resultado = $usuario->existeUsuario();

                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();

                } else {
                    // Hashear password
                    $usuario->hashPassword();

                    // Generar un token único
                    $usuario->crearToken();

                    // // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar();

                }
            } else {
                echo json_encode(["msg" => $alertas]);
            }

            echo json_encode(["msg" => "Usuario creado correctamente, revise su email", "usuario" => $usuario, "alertas" => $alertas]);
        }
    }

    public static function confirmar()
    {
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token No Válido');
        } else {
            // Modificar a usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        // Obtener alertas
        $alertas = Usuario::getAlertas();
        echo json_encode(["msg" => $alertas]);


    }
}
