<?php

namespace Model;

class Ingrediente extends ActiveRecord {
    protected static $tabla = "ingredientes";
    protected static $columnasDB = [
        "id",
        "nombre",
        "descripcion"
    ];
    public $id;
    public $nombre;
    public $descripcion;
   

    public function __construct($args = []){
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->descripcion = $args["descripcion"] ?? "";
    }

    // Mensajes de validación para el formulario de creación
    public function validar() : array{
        if(!$this->nombre){
            self::$alertas =["result" => "error", "msg" => "El nombre es obligatorio"]  ;
        }
        if (!$this->descripcion) {
            self::$alertas =["result" => "error", "msg" => "Una pequeña descripción"]  ;

        }
        return self::$alertas;
    }

    public static function ingredientesPorReceta($id){
        $query = "
        SELECT i.id, i.nombre, ri.cantidad 
        FROM ingredientes i 
        INNER JOIN receta_ingrediente ri ON i.id = ri.id_ingrediente 
        WHERE ri.id_receta = " . $id . ";";
        $resultado = self::$db->query($query);
        $ingredientes = [];
       while($ingrediente = $resultado->fetch_assoc()){
        $ingredientes[] = $ingrediente;
       }
  
 
        
        return $ingredientes;

    }
    
    

}
