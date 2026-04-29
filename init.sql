-- 1. Crear la base de datos:

create database if not exists proyecto DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Seleccionamos la base de datos "proyecto"

use proyecto;

-- 3. Creamos las tablas

CREATE TABLE if not exists CLIENTES(
    email VARCHAR(100) PRIMARY KEY,
    contrasena VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    DNI VARCHAR(20) NOT NULL,
    telefono INT(15),
    foto BLOB,
    codigo VARCHAR(100) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 0
);

CREATE TABLE if not exists GESTORES(
    email VARCHAR(100) PRIMARY KEY,
    contrasena VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    DNI VARCHAR(20) NOT NULL,
    telefono INT(15),
    foto BLOB,
    administrador TINYINT(1)
);

CREATE TABLE if not exists PISTAS(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    localizacion VARCHAR(100) NOT NULL,
    precioReserva DECIMAL(10, 2) NOT NULL
);

CREATE TABLE if not exists RESERVAS(
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    horaInicio TIME NOT NULL,
    horaFin TIME NOT NULL,
    pista INT NOT NULL,
    cliente VARCHAR(100),
    informacion VARCHAR(255) NOT NULL,
    CONSTRAINT fk_res_pist FOREIGN KEY(pista) REFERENCES pistas(id),
    CONSTRAINT fk_res_cli FOREIGN KEY(cliente) REFERENCES clientes(email)
);

CREATE TABLE if not exists SUGERENCIAS_INCIDENCIAS(
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE,
    contenido VARCHAR(500),
    cliente VARCHAR(100),
    CONSTRAINT fk_sug_cli FOREIGN KEY(cliente) REFERENCES clientes(email)
);

CREATE TABLE if not exists CONEXIONES(
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(100),
    hora INT(100),
    acceso VARCHAR(100)
);

-- 4.- Creamos un usuario

create user gestor@'localhost' identified by "secreto";

-- 5.- Le damos permiso en la base de datos "proyecto"

grant all on proyecto.* to gestor@'localhost';