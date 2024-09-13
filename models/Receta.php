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


    public function __construct($args = []) {
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->instrucciones = $args["instrucciones"] ?? "";
        $this->imagen = $args["imagen"] ?? "";
        $this->origen = $args["origen"] ?? "";

    }

    // Mensajes de validación para la creación de una cuenta
    public function validar(): array {
        if (!$this->nombre) {
            self::$alertas["error"][] = "El nombre es obligatorio";
        }
        if (!$this->instrucciones) {
            self::$alertas["error"][] = "Algunas instrucciones son necesarias";
        }
        return self::$alertas;
    }

    public static function recetasCompletas() {

        $query = "
        SELECT r.*, i.id AS ingrediente_id, i.nombre AS ingrediente_nombre
        FROM recetas r
        INNER JOIN receta_ingrediente ri ON r.id = ri.id_receta
        INNER JOIN ingredientes i ON i.id = ri.id_ingrediente
        
    ";
        $resultado = self::$db->query($query);

        if (!$resultado) {
            return [
                'resultado' => 'error',
                'mensaje' => 'Error en la consulta: ' . self::$db->error
            ];
        } else {
            return $resultado;
        }

    }

    public static function getByName($nombre) {
        $query = "SELECT * FROM " . self::$tabla . " WHERE nombre like '%" . $nombre . "%'";
        $resultado = static::consultarSQL($query);

        return array_shift($resultado);
    }


}
