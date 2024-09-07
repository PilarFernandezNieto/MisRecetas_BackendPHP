<?php

namespace Model;

use Exception;

class Usuario extends ActiveRecord {
    protected static $tabla = "usuarios";
    protected static $columnasDB = [
        "id",
        "nombre",
        "apellidos",
        "email",
        "password",
        "admin",
        "confirmado",
        "token"
    ];
    public $id;
    public $nombre;
    public $apellidos;
    public $email;
    public $password;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []){
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->apellidos = $args["apellidos"] ?? "";
        $this->email = $args["email"] ?? "";
        $this->password = $args["password"] ?? "";
        $this->admin = $args["admin"] ?? 0;
        $this->confirmado = $args["confirmado"] ?? 0;
        $this->token = $args["token"] ?? "";
    }

    // Mensajes de validación para la creación de una cuenta
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas["error"][] =  "El nombre es obligatorio";
        }
        if (!$this->apellidos) {
            self::$alertas["error"][] =  "El apellido es obligatorio";
        }
        if (!$this->email) {
            self::$alertas["error"][] =  "El email es obligatorio";
        }
        if (!$this->password) {
            self::$alertas["error"][] =  "El password es obligatorio";
        }
        if(strlen($this->password) < 6){
            self::$alertas["error"][] = "El password debe contener al menos 6 caracteres";
        }

        return self::$alertas;
    }
    public function validarEmail(){
        if (!$this->email) {
            self::$alertas = "El email es obligatorio";
        }
        return self::$alertas;
    }
    public function validarPassword(){
        if(!$this->password){
            self::$alertas = "El password es obligatorio";
        }
            return self::$alertas;
    }
    public function comprobarPassword($password){
        $resultado = password_verify($password, $this->password);
        if(!$resultado) {
            throw new Exception("La contraseña no es correcta", 400);
        }
        return true;
    }
    public function verificarCuentaConfirmada(){
        if(!$this->confirmado){
            throw new Exception("La cuenta no ha sido confirmada", 409);
        }
       return true;
    }


    public function existeUsuario(){
        $qry = "SELECT * FROM ". self::$tabla ." WHERE email = '".$this->email. "' LIMIT 1";
        $resultado = self::$db->query($qry);
        if ($resultado->num_rows > 0) {
            throw new Exception("El usuario ya está registrado.");
        }
        return $resultado;
    }

        
        
    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(): void{
        $this->token = uniqid();
    }



}
