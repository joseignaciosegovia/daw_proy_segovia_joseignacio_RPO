<?php
namespace Clases;

use PDO;
use PDOException;

class DB {
    private $con;
	private $bbdd;
	private $host = "localhost";
 	private $usu = "gestor";
	private $clave = "secreto";
	public $datos;          // Devolverá un array con los datos de la consulta
	 
    // El constructor recibirá el nombre de la BBDD a conectar y realizará la conexión
	public function __construct($base) {
		try {
			$this->bbdd = $base;
        	$this->con = new PDO("mysql:host=$this->host;dbname=$this->bbdd",$this->usu,$this->clave);
        	$this->con->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        	$this->con->exec("set names utf8mb4");
        } 
	  	catch(PDOException $e) {  
            echo "  <p>Error: No puede conectarse con la base de datos.</p>\n\n";
            echo "  <p>Error: " . $e->getMessage() . "</p>\n";
            exit();
        }
	}
	// Para las consultas que no devuelvan datos  
	public function ConsultaSimple($consulta) {

		$this->con->exec($consulta);
	}
	// Para las consultas que devuelven datos 
	public function Consulta($consulta) {
		
		$resultado = $this->con->query($consulta);
		while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)) {
			$this->datos[] = $fila;
		}
	}

	public function Cerrar() {
		$this->con = null;
	}
}