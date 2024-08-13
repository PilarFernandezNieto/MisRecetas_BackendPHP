<?php

namespace Model;

class Receta extends ActiveRecord {
    protected static $tabla = "recetas";
    protected static $columnasDB = [
        "id",
        "nombre",
        "instrucciones",
        "imagen",
        "origen"
    ];
    public $id;
    public $nombre;
    public $instrucciones;
    public $imagen;
    public $origen;
    public $ingredientes;
   

    public function __construct($args = []){
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->instrucciones = $args["instrucciones"] ?? "";
        $this->imagen = $args["imagen"] ?? "";
        $this->origen = $args["origen"] ?? "";
        $this->ingredientes = $args["ingredientes"] ?? "";
    }

    // Mensajes de validación para la creación de una cuenta
    public function validar(){
        if(!$this->nombre){
            self::$alertas["error"][] =  "El nombre es obligatorio";
        }
        if (!$this->instrucciones) {
            self::$alertas["error"][] =  "Algunas instrucciones son necesarias";
        }
        return self::$alertas;
    }

    



    

}
