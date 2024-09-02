<?php

namespace Model;
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];

    // Alertas y Mensajes
    protected static $alertas = [];

    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    /**
     * @param $result
     * @param $mensaje
     * @return void
     * Crea una alerta personalizada de tipo error o success
     */
    public static function setAlerta($result, $mensaje) :void {
        static::$alertas["result"] = $result;
        static::$alertas["msg"] = $mensaje;
    }

    /**
     * @return array
     * Obtiene todas las alertas del modelo
     */
    public static function getAlertas() : array {
        return static::$alertas;
    }

    public function validar() :array {
        static::$alertas = [];
        return static::$alertas;
    }

    /**
     * @param $query
     * @return array
     * Consulta SQL que crea un objeto en memoria y devuelve en forma de array
     */
    public static function consultarSQL($query) :array {
        $resultado = self::$db->query($query);

        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            if (isset($registro['id'])) { // Me aseguro de que me devuelve el id como int
                $registro['id'] = (int)$registro['id'];
            }
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;
    }

    /**
     * @param $registro
     * @return static
     * Crea un objeto en memoria igual al de la base de datos
     */
    protected static function crearObjeto($registro) :static {
        $objeto = new static;

        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }
        return $objeto;
    }

    /**
     * @return array
     * Identifica y une los atributos recibidos para añadir con los de la base de datos
     */
    public function atributos() :array {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            if ($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    /**
     * @return array
     * Sanitiza los datos antes de añadir a la base de datos
     */
    public function sanitizarAtributos() :array {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->escape_string($value ?? "");
        }
        return $sanitizado;
    }

    /**
     * @param $args
     * @return void
     * Sincroniza la base de datos con los objetos en memoria
     */
    public function sincronizar($args = []) :void {

        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }

    }

    /**
     * @return array|mixed
     * Función que crea o actualiza un registro en función de si tiene id o no
     */
    public function guardar() :mixed {
        $resultado = "";
        if (!is_null($this->id)) {
            $resultado = $this->actualizar();
        } else {
            $resultado = $this->crear();
        }

        return $resultado;
    }

    /**
     * @param string $order
     * @param int $limit
     * @return array
     * Devuelve todos los registros de una tabla
     */
    public static function all(string $order = "", int $limit = 0) {
        $orderBy = (!empty($order) ? " ORDER BY " . $order : "");
        $limit = $limit > 0 ? " LIMIT " . $limit : "";
        $query = "SELECT * FROM " . static::$tabla . $orderBy . $limit;
        return self::consultarSQL($query);
    }

    /**
     * @param $id
     * @return mixed|null
     * Devuelve un registro por su id
     */
    public static function find($id) :mixed {
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = " . $id;
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    /**
     * @param $limite
     * @return mixed|null
     * Devuelve un número limitado de registros
     */
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT $limite";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    /**
     * @param $columna
     * @param $valor
     * @return mixed|null
     * Busca un registro por un campo especificado por parámetro
     */
    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE " . $columna . " = '" . $valor . "'";
        //return json_encode(["query" => $query]); // debuguear fetch
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    /**
     * @param $query
     * @return array
     * Consulta plana sql
     */
    public static function SQL($query) {
        $resultado = self::consultarSQL($query);
        return $resultado;
    }


    /**
     * @return array
     * Añade un registro a la base de datos con los atributos a añadir ya sanitizados
     * También mantiene los valores numéricos de los datos a añadir
     */
    public function crear() :array {
        $atributos = $this->sanitizarAtributos();
        $columnas = [];
        $valores = [];

        foreach ($atributos as $key => $value) {
            $columnas[] = $key;
            if (is_numeric($value) || is_int($value) || is_float($value) || is_bool($value)) {
                $valores[] = $value;
            } else {
                $valores[] = "'" . self::$db->real_escape_string($value) . "'";
            }
        }
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', $columnas);
        $query .= " ) VALUES (";
        $query .= join(', ', $valores);
        $query .= ") ";

        $resultado = self::$db->query($query);


        return [
            'resultado' => $resultado,
            'id' => (int)self::$db->insert_id
        ];
    }

    /**
     * @return mixed
     * Actualiza un registro de la base datos con los valores a actualizar sanitizados
     * También mantiene los valores numéricos
     */
    public function actualizar() : mixed {

        $atributos = $this->sanitizarAtributos();
        $valores = [];
        foreach ($atributos as $key => $value) {

            if (is_numeric($value)) {
                $valores[] = "{$key} =" . $value;
            } else {
                $valores[] = "{$key}='" . self::$db->real_escape_string($value) . "'";
            }

        }

        $query = "UPDATE " . static::$tabla . " SET ";
        $query .= join(', ', $valores);
        $query .= " WHERE id = " . (int)self::$db->escape_string($this->id) . " ";
        $query .= " LIMIT 1 ";


        //return json_encode(["query" => $query]); // debuguear fetch
        $resultado = self::$db->query($query);
        return $resultado;
    }

    /**
     * @return array
     * Elimina un registro por su id
     */
    public function eliminar() :array{
        $query = "DELETE FROM " . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        return [
            "resultado" => $resultado,
            "id" => $this->id
        ];
    }



}