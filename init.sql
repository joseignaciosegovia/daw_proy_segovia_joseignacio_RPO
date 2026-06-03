-- 1. Creación de la base de datos:

create database if not exists proyecto DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Selección de la base de datos "proyecto"

use proyecto;

-- 3. Creación de las tablas

CREATE TABLE if not exists clientes(
    email VARCHAR(100) PRIMARY KEY,
    contrasena VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    DNI VARCHAR(20) NOT NULL,
    telefono INT(15),
    foto VARCHAR(255),
    codigo VARCHAR(100) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 0
);

CREATE TABLE if not exists gestores(
    email VARCHAR(100) PRIMARY KEY,
    contrasena VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    DNI VARCHAR(20) NOT NULL,
    telefono INT(15),
    foto VARCHAR(255),
    administrador TINYINT(1)
);

CREATE TABLE if not exists pistas(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    localizacion VARCHAR(100) NOT NULL,
    precioReserva DECIMAL(10, 2) NOT NULL
);

CREATE TABLE if not exists reservas(
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    horaInicio TIME NOT NULL,
    horaFin TIME NOT NULL,
    pista INT NOT NULL,
    cliente VARCHAR(100),
    informacion VARCHAR(255) NOT NULL,
    idPago VARCHAR(255) NOT NULL,
    estadoPago VARCHAR(200),
    CONSTRAINT fk_res_pist FOREIGN KEY(pista) REFERENCES pistas(id),
    CONSTRAINT fk_res_cli FOREIGN KEY(cliente) REFERENCES clientes(email)
);

CREATE TABLE if not exists sugerencias_incidencias(
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE,
    contenido VARCHAR(500),
    cliente VARCHAR(100),
    CONSTRAINT fk_sug_cli FOREIGN KEY(cliente) REFERENCES clientes(email)
);

CREATE TABLE if not exists conexiones(
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(100),
    hora INT(100),
    acceso VARCHAR(100)
);

SET NAMES utf8mb4;

-- 4. Inserción de datos iniciales

-- Tabla clientes

INSERT INTO clientes VALUES 
    ('marLop@gmail.com', '$2y$10$lpTs.g49VSLKkD8SGzl9LOVpZ7UNFwmecxGC9PqSioXlREtjGp.r2', 'María López', '87182344I', 655871025, "/imagenes/blank-profile-picture.png", '$2y$12$GRoxOUyMK.YyXPot2/2F..7Q8zTrzrlY4y2k9CZMNmPFgoOiMHq6y', 1),
    ('ferSan@gmail.com', '$2y$10$jEGR/YfIt9Xiu5r/rjrreOYVwISNnJxYKGmybCdmwkDBYoR.XGyCW', 'Fernando Sanz', '11298419O', 664788795, "/imagenes/blank-profile-picture.png", '$2y$10$9T21merzMQpflmLCKtA0v.nlFN05ci5S5042b8BPbD6S4VLQgmOtG', 1),
    ('antGar@gmail.com', '$2y$10$DMMANtYfLFFAMXhnIzH7q.jlB7yyHam18wGwUZU1T.Z0o3CJl3qdu', 'Antonio García', '74439120U', 697874169, "/imagenes/blank-profile-picture.png", '$2y$10$vgm9646Cb6zlRNT6NKFGeeI4/0rihPFbtv3Sr1y8LW6rE.FyZ/HhC', 1),
    ('ferLui@gmail.com', '$2y$10$u4AZ1xTiDSyXublcDmqv3.8UOt2SXdaeiRCjUPwQdNDIQEyM5pHCC', 'Fernanda Luisa', '11298437H', 604975301, "/imagenes/blank-profile-picture.png", '$2y$10$9T21merzMQpflmLCKtA0v.nlFN05ci5S5042b8BPbD6S4VLQgmOtG', 1);

-- Contraseñas de clientes: 
-- 'marL1234'
-- 'ferS1234'
-- 'antG1234'
-- 'ferL1234'

INSERT INTO gestores VALUES 
    ('adminMer@gmail.com', '$2y$10$LJpRbN7ZgtL1fgQeQh/tc.l6Hu49y6p/5tKzXZ8f.O/bDcvpWqPC2', 'Mercedes Puertas', '77319284T', 661281938, "/imagenes/blank-profile-picture.png", 1),
    ('adminAnton@gmail.com', '$2y$10$xnq7nlX9FhSC7MQQbDzkW.jDo0irPlmkPCaFh59AZiOLkOIf93abO', 'Antonio Castillo', '71822198U', 617291009, "/imagenes/blank-profile-picture.png", 0);

-- Contaseñas de gestores:
-- 'admMer12'
-- 'admAn123'

INSERT INTO pistas (nombre, localizacion, precioReserva) VALUES 
    ('Campo fútbol 7', 'Ciudad Deportiva', 8.25),
    ('Campo fútbol 11', 'Ciudad Deportiva', 9),
    ('Pádel', 'Ciudad Deportiva', 7.30),
    ('Atletismo', 'Ciudad Deportiva', 8),
    ('Multiusos', 'Ciudad Deportiva', 5),
    ('Pista interna', 'Polideportivo', 4),
    ('Pista externa', 'Polideportivo', 4);

-- 5.- Creación de un usuario

create user gestor@'%' identified by "secreto";

-- 6.- Le damos permiso en la base de datos "proyecto"

grant all on proyecto.* to gestor@'%';