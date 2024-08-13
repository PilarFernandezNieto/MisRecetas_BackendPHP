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

  

    

}
