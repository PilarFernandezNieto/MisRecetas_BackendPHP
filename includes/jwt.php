<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Model\Usuario;


function generateJwt($userId, $isAdmin) {
    $secretKey = $_ENV["JWT_SECRET"];
    $issuedAt = time();
    $expiration = $issuedAt + (30 * 24 * 60 * 60); // Expiración en 30 días
    $payload = [
        'iss' => 'mis_recetas', // Emisor del token
        'iat' => $issuedAt, // Tiempo en que fue emitido
        'exp' => $expiration, // Tiempo en que expirará
        'sub' => $userId,
        "role" => $isAdmin ? "admin" : "user"// ID del usuario
    ];
    return JWT::encode($payload, $secretKey, 'HS256');
}

function verificarJWT($headers) {
    $secretKey = $_ENV["JWT_SECRET"];
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        try {
            $token = str_replace('Bearer ', '', $authHeader);
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            $user = Usuario::find($decoded->sub);
            $userData = [
                "id" => $user->id,
                "nombre" => $user->nombre,
                "apellido" => $user->apellidos,
                "email" => $user->email,
                "admin" => $user->admin,
            ];
            if (!$user) {
                http_response_code(404);
                echo json_encode(["result" => "error", "msg" => "Usuario no encontrado"]);
                return null;
            }
            return $userData;
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode(["result" => "error", "msg" => "Token no válido", "exception" => $e->getMessage()], JSON_UNESCAPED_UNICODE);

        }
    } else {
        http_response_code(403);
        echo json_encode(["msg" => "Token no válido o inexistente"], JSON_UNESCAPED_UNICODE);

    }
}


function onlyAdmin($user) {

    if (!$user) {
        http_response_code(401);
        echo json_encode(["msg" => "Acceso no autorizado"], JSON_UNESCAPED_UNICODE);
        return null;
    }

    if (!$user["admin"]) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode( "Acceso denegado: se requiere rol de administrador", JSON_UNESCAPED_UNICODE);
        die();
    }

    return $user;
}
