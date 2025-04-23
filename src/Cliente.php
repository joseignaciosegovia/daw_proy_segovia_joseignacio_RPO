<?php
namespace Clases;

class Cliente {
    private $email;
    private $contraseña;
    private $nombre;
    private $telefono;

    public function __construct ($email, $contraseña, $nombre, $telefono) {
        $this->email = $email;
        $this->contraseña = $contraseña;
        $this->nombre = $nombre;
        $this->telefono = $telefono;
    }

    public function __get($property){
        if(property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value){
        if(property_exists($this, $property)) {
            $this->$property = $value;
        }
    }
}