-- ============================================================

-- Importante: indica la codificación de los textos de este archivo.
-- Sin esta línea, algunos importadores (phpMyAdmin, MySQL Workbench)
-- pueden guardar los acentos con la codificación equivocada.

SET NAMES utf8mb4;

-- Práctica Evaluada II - Ejercicio 2: Gestor de Contactos
-- Base de datos: gestor_contactos
-- Ejecutar este archivo ANTES de programar (README lo indica).
-- ============================================================

CREATE DATABASE IF NOT EXISTS gestor_contactos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gestor_contactos;

-- Tabla 1: catálogo de tipos de contacto
CREATE TABLE IF NOT EXISTS tipos_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE = InnoDB;

-- Tabla 2: contactos
CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    tipo_contacto_id INT NOT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_contactos_tipo
        FOREIGN KEY (tipo_contacto_id) REFERENCES tipos_contacto (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB;

-- Tipos de contacto por defecto
INSERT INTO tipos_contacto (nombre) VALUES
    ('Amigo'),
    ('Familia'),
    ('Trabajo'),
    ('Otro')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- Datos de ejemplo (opcionales, para probar el listado)
INSERT INTO contactos (nombre, email, telefono, tipo_contacto_id) VALUES
    ('María López',    'maria.lopez@example.com',  '70112233', 1),
    ('Carlos Ruiz',    'carlos.ruiz@example.com',  '70114455', 3),
    ('Ana Gómez',      'ana.gomez@example.com',    '70116677', 2),
    ('Luis Herrera',   'luis.herrera@example.com',  '70118899', 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
