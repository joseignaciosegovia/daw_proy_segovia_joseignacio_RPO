<?php

require_once "../modelo/Conexion.inc.php";

class Crud {
    private $base;

    function __construct($base) {
        $this->base = $base;
    }
    function insertar($tabla, $valores) {
        $consulta = "insert into " . $tabla . " values (" . $valores . ")";

        try {
            $this->base->ConsultaSimple($consulta);
        } catch (PDOException $ex) {
            die("Ocurrió un error al insertar datos en la tabla " . $tabla . ": " . $ex->getMessage());
        }
    }

    function actualizar($tabla, $valores, $condicion) {
        $consulta = "update " . $tabla . " set " . $valores . " where " . $condicion;
        try {
            $this->base->ConsultaSimple($consulta);
        } catch (PDOException $ex) {
            die("Ocurrio un error al actualizar los datos de la tabla " . $tabla . ": " . $ex->getMessage());
        }
    }

    function eliminar($tabla, $condicion) {
        $consulta = "delete from " . $tabla . " where " . $condicion;
        
        try {
            $this->base->ConsultaSimple($consulta);
        } catch (PDOException $ex) {
            die("Ocurrio un error al borrar un registro de la tabla " . $tabla . ": " . $ex->getMessage());
        }
    }

    function obtener($tabla, $condicion) {
        $consulta = "select * from " . $tabla . " where " . $condicion;

        try {
            $this->base->Consulta($consulta);
        } catch (PDOException $ex) {
            die("Ocurrió un error al insertar datos en la tabla " . $tabla . ": " . $ex->getMessage());
        }

        return $this->base->datos;
    }

    function listar($tabla, $condicion) {

        // Limpiamos los datos
        $this->base->datos = null;
        
        $consulta = "select * from " . $tabla . $condicion;
        
        $this->base->Consulta($consulta);
        $data = array();
        if($this->base->datos != null){
            // Recorremos el array "datos" (donde están los registros de la tabla)
            foreach ($this->base->datos as $value) {
                // En cada iteración, guardamos los datos de cada registro en el array "data"
                array_push($data, $value);
            }
            return $data;
        }
    }

    // Comprueba si el acceso ha sido correcto
    function isValido($tabla, $email, $contraseña) {
        try {
            // Obtenemos la contraseña del documento que tiene el nombre de usuario introducido
            $resultado = $this->obtener($tabla, "email = \"$email\"")[0];

            // Si se ha encontrado un usuario
            if($resultado) {
                // Comprobamos que la contraseña es correcta
                if(password_verify($contraseña, $resultado['contraseña'])) {
                    // Devolvemos los datos del usuario
                    return $resultado;
                }
                // Si la contraseña es incorrecta
                else {
                    return null;
                }
            }
            // Si no se ha encontrado un usuario
            else{
                return null;
            }
            
        } catch(\Throwable $th) {
            return $th->getMessage();
        }
    }
}