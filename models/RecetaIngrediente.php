<?php

namespace Model;

class RecetaIngrediente extends ActiveRecord {
    protected static $tabla = "receta_ingrediente";
    protected static $columnasDB = [
        "id",
        "id_receta",
        "id_ingrediente",
        "cantidad"
    ];
    public $id;
    public $id_receta;
    public $id_ingrediente;
    public $cantidad;
    
   

    public function __construct($args = []){
        $this->id = $args["id"] ?? null;
        $this->id_receta = $args["id_receta"] ?? null;
        $this->id_ingrediente = $args["id_ingrediente"] ?? null;
        $this->cantidad = $args["cantidad"] ?? "";
    }

  public static function ingredientesPorReceta($id_receta){
    $query = "
        SELECT * 
        FROM receta_ingrediente 
        WHERE id_receta = ". $id_receta. "
    ";
    $resultado = self::SQL($query);
    if (!$resultado) {
        return [
            'resultado' => 'error',
            'mensaje' => 'Error en la consulta: ' . self::$db->error
        ];
    } else {

        return $resultado;
    }

  }
  public static function eliminarIngredienteDeReceta($id_receta, $id_ingrediente){
    $query = "
    DELETE FROM receta_ingrediente WHERE id_receta = $id_receta AND id_ingrediente = $id_ingrediente
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


    

}
