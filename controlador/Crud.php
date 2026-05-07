<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/modelo/Conexion.inc.php";

class Crud {
    private $base;

    function __construct($base) {
        $this->base = $base;
    }

    function crearTabla($tabla, $columnas) {
        $consulta = "create table if not exists " . $tabla . "(" . $columnas . ")";

        try {
            $this->base->ConsultaSimple($consulta);
        } catch (PDOException $ex) {
            throw new PDOException("No puede conectarse con la base de datos: " . $ex->getMessage());
        }
    }

    function insertar($tabla, $valores) {
        $consulta = "insert into " . $tabla . " values (" . $valores . ")";

        try {
            $this->base->ConsultaSimple($consulta);
        } catch (PDOException $ex) {
            die("Ocurrió un error al insertar datos en la tabla " . $tabla . ": " . $ex->getMessage());
        }
    }

    function insertarColumnas($tabla, $columnas, $valores) {
        $consulta = "insert into " . $tabla . " " . $columnas . " values (" . $valores . ")";

        try {
            $this->base->ConsultaSimple($consulta);
        } catch (PDOException $ex) {
            die("Ocurrió un error al insertar datos en la tabla " . $tabla . ": " . $ex->getMessage());
        }
    }

    function actualizar($tabla, $valores, $condicion) {
        $consulta = "update " . $tabla . " set " . $valores . " " . $condicion;
        try {
            $this->base->ConsultaSimple($consulta);
        } catch (PDOException $ex) {
            die("Ocurrio un error al actualizar los datos de la tabla " . $tabla . ": " . $ex->getMessage());
        }
    }

    function eliminar($tabla, $condicion) {
        $consulta = "delete from " . $tabla . " " . $condicion;
        
        try {
            $this->base->ConsultaSimple($consulta);
        } catch (PDOException $ex) {
            die("Ocurrio un error al borrar un registro de la tabla " . $tabla . ": " . $ex->getMessage());
        }
    }

    function obtener($tabla, $condicion) {

        // Limpiamos los datos
        $this->base->datos = null;
        
        $consulta = "select * from " . $tabla . " " . $condicion;

        try {
            $this->base->Consulta($consulta);
        } catch (PDOException $ex) {
            die("Ocurrió un error al insertar datos en la tabla " . $tabla . ": " . $ex->getMessage());
        }

        return $this->base->datos;
    }

    function listar($seleccion, $tabla, $condicion) {

        // Limpiamos los datos
        $this->base->datos = null;
        
        $consulta = "select " . $seleccion . " from " . $tabla . " " . $condicion;
        try {
            $this->base->Consulta($consulta);
        } catch (PDOException $ex) {
            die("Ocurrió un error al listar datos de la tabla " . $tabla . ": " . $ex->getMessage());
        }

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
            $resultado = $this->obtener($tabla, "where email = \"$email\"")[0];

            // Si se ha encontrado un usuario
            if($resultado) {
                // Comprobamos que la contraseña es correcta
                if(password_verify($contraseña, $resultado['contrasena'])) {
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