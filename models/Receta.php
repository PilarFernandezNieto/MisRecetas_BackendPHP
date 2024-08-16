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


    public function __construct($args = []) {
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->instrucciones = $args["instrucciones"] ?? "";
        $this->imagen = $args["imagen"] ?? "";
        $this->origen = $args["origen"] ?? "";
        $this->ingredientes = $args["ingredientes"] ?? "";
    }

    // Mensajes de validación para la creación de una cuenta
    public function validar() {
        if (!$this->nombre) {
            self::$alertas["error"][] =  "El nombre es obligatorio";
        }
        if (!$this->instrucciones) {
            self::$alertas["error"][] =  "Algunas instrucciones son necesarias";
        }
        return self::$alertas;
    }

    public function recetaCompleta($id) {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            return [
                'resultado' => 'error',
                'mensaje' => 'ID inválido'
            ];
        }
        $query = "
        SELECT r.*, i.id AS ingrediente_id, i.nombre AS ingrediente_nombre
        FROM recetas r
        INNER JOIN receta_ingrediente ri ON r.id = ri.id_receta
        INNER JOIN ingredientes i ON i.id = ri.id_ingrediente
        WHERE r.id = $id
    ";
        $resultado = self::$db->query($query);
        if (!$resultado) {
            return [
                'resultado' => 'error',
                'mensaje' => 'Error en la consulta: ' . self::$db->error
            ];
        }

        while ($registro = $resultado->fetch_assoc()) {
            debuguear($registro);
            $this->ingredientes = [
                'ingrediente_id' => $registro["ingrediente_id"],
                'ingrediente_nombre' => $registro["ingrediente_nombre"]
            ];
        }
       
        return [
            'receta_id' => $id,
            'receta_nombre' => $registro["nombre"] ?? '',
            'instrucciones' => $registro["instrucciones"] ?? '',
            'imagen' => $registro["imagen"] ?? '',
            'origen' => $registro["origen"] ?? '',
            'ingredientes' => $this->ingredientes
        ];
     
    }
}
