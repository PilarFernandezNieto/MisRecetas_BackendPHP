<?php

namespace Controllers;

use Clases\Email;
use Exception;
use Model\Usuario;

class AuthController {


    public static function login(): void {
        $resultado = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();
            $alertas = $auth->validarPassword();
            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario) {
                    if (!$usuario->confirmado) {
                        $resultado = [
                            "result" => "error",
                            "msg" => "La cuenta todavía no ha sido confirmada"
                        ];

                    } else {
                        try {
                            $usuario->comprobarPassword($auth->password);
                            $isAdmin = $usuario->admin;
                            $token = generateJwt($usuario->id, $isAdmin);
                            $resultado = [
                                "token" => $token
                            ];

                        } catch (Exception $e) {
                            http_response_code(409);
                            $resultado = [
                                "result" => "error",
                                "msg" => $e->getMessage()
                            ];
                        }
                    }
                } else {
                    $resultado = [
                        "result" => "error",
                        "msg" => "Usuario no encontrado"
                    ];
                }

            } else {
                $resultado =
                    [
                        "result" => "error",
                        "msg" => Usuario::getAlertas()
                    ];
            }
            echo json_encode($resultado);
        }
    }

    public static function registro(): void {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $usuario = new Usuario();
            $alertas = [];

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $usuario->sincronizar($_POST);

                $alertas = $usuario->validarNuevaCuenta();

            }
            if (empty($alertas)) {
                try {
                    $usuario->existeUsuario();
                    $usuario->hashPassword();
                    $usuario->crearToken();
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    $resultado = $usuario->guardar();
                    echo json_encode(["msg" => "Usuario creado correctamente, revise su email", "usuario" => $usuario, "alertas" => $alertas]);

                } catch (Exception $e) {
                    http_response_code(409);
                    echo json_encode(["result" => "error", "msg" => $e->getMessage()]);
                }
            } else {
                echo json_encode(["msg" => $alertas]);
            }
        }
    }

    public static function confirmar($token) {
        $alertas = [];

        $token = s($token);

        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta("error", "Token no encontrado");
        } else {

            $usuario->confirmado = 1;
            $usuario->token = null;
            $usuario->guardar();

            Usuario::setAlerta('success', 'Cuenta Confirmada Correctamente');
        }

        // Obtener alertas
        $alertas = Usuario::getAlertas();
        echo json_encode($alertas);


    }

    public static function user(){
        $headers = getallheaders();
        $user = verificarJWT($headers);
        $user = onlyAdmin($user);
        echo json_encode($user);
    }
}
