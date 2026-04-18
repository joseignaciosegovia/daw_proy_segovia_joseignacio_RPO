-- 1. Crear la base de datos:

create database proyecto DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Seleccionamos la base de datos "proyecto"

use proyecto;

-- 3. Creamos las tablas

CREATE TABLE if not exists CLIENTES(
    email VARCHAR(100) PRIMARY KEY,
    contrasena VARCHAR(100) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    DNI VARCHAR(100) NOT NULL,
    telefono INT(9),
    foto BLOB,
    codigo VARCHAR(100) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 0
);

CREATE TABLE if not exists GESTORES(
    email VARCHAR(100) PRIMARY KEY,
    contrasena VARCHAR(100) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    DNI VARCHAR(100) NOT NULL,
    telefono INT(9),
    foto BLOB,
    administrador TINYINT(1)
);

CREATE TABLE if not exists PISTAS(
    id MEDIUMINT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    localizacion VARCHAR(100) NOT NULL,
    precioReserva DECIMAL(10, 2) NOT NULL
);

CREATE TABLE if not exists RESERVAS(
    id MEDIUMINT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE NOT NULL,
    horaInicio TIME NOT NULL,
    horaFin TIME NOT NULL,
    pista MEDIUMINT NOT NULL,
    cliente VARCHAR(100),
    informacion VARCHAR(100) NOT NULL,
    CONSTRAINT Res_pis_FK FOREIGN KEY(pista) REFERENCES PISTAS(id),
    CONSTRAINT Res_Cli_FK FOREIGN KEY(cliente) REFERENCES CLIENTES(email)
);

CREATE TABLE if not exists SUGERENCIAS_INCIDENCIAS(
    id MEDIUMINT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE,
    contenido VARCHAR(100),
    cliente VARCHAR(100),
    CONSTRAINT Sug_Cli_FK FOREIGN KEY(cliente) REFERENCES CLIENTES(email)
);

CREATE TABLE if not exists CONEXIONES(
    id MEDIUMINT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(100),
    hora INT(100),
    acceso VARCHAR(100)
);

-- 4.- Creamos un usuario

create user gestor@'localhost' identified by "secreto";

-- 5.- Le damos permiso en la base de datos "proyecto"

grant all on proyecto.* to gestor@'localhost';

-- 6. Borrar las tablas

drop table conexiones;
drop table sugerencias_incidencias;
drop table reservas;
drop table pistas;
drop table gestores;
drop table clientes;