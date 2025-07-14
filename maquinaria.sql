
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS inventario_maquinaria;
USE inventario_maquinaria;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insertar usuario por defecto
INSERT INTO usuarios (username, password)
VALUES ('admin', SHA2('1234', 256));

-- Tabla de maquinaria sin campo descripcion
CREATE TABLE IF NOT EXISTS maquinaria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('nueva', 'usada') NOT NULL DEFAULT 'nueva',
    modelo VARCHAR(100),
    numero_serie VARCHAR(100),
    marca VARCHAR(100),
    anio INT,
    ubicacion VARCHAR(100),
    condicion_estimada INT,
    imagen VARCHAR(255),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
