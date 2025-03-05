-- Eliminar la base de datos si ya existe
DROP DATABASE IF EXISTS db_main;

-- Crear la base de datos
CREATE DATABASE db_main;
USE db_main;

-- Crear usuario de MySQL con privilegios completos
DROP USER IF EXISTS 'admpos'@'%';
CREATE USER 'admpos'@'%' IDENTIFIED BY 'Pos2025!';
GRANT ALL PRIVILEGES ON db_main.* TO 'admpos'@'%';
FLUSH PRIVILEGES;

--  Desactivar la integridad referencial antes de crear tablas
SET FOREIGN_KEY_CHECKS = 0;

-- Crear tabla de empresas
CREATE TABLE empresa (
    id_empresa INT PRIMARY KEY AUTO_INCREMENT,
    uuid_empresa CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()), -- Para sincronización con la nube
    razon_social VARCHAR(100) NOT NULL,
    ruc VARCHAR(13) UNIQUE NOT NULL,
    direccion TEXT,
    telefono VARCHAR(20),
    email VARCHAR(100) UNIQUE NOT NULL,
    email_facturacion VARCHAR(100),
    cod_actividad VARCHAR(10),
    regimen_tributacion VARCHAR(20),
    clave_acceso VARBINARY(255) NOT NULL, --  Encriptada con AES-256
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

--  Crear tabla de roles
CREATE TABLE rol (
    id_rol INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) UNIQUE NOT NULL
);

-- Crear tabla de parámetros (preguntas secretas, configuraciones generales)
CREATE TABLE parametro (
    id_parametro INT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(50) NOT NULL,  --  Tipo de parámetro ('pregunta_secreta', 'configuracion')
    clave VARCHAR(50) NOT NULL, --  Clave única ('PREG_01', 'MAX_INTENTOS_LOGIN')
    valor TEXT NOT NULL,        --  Almacena cualquier tipo de dato como texto
    formato ENUM('string', 'int', 'float', 'bool', 'date', 'datetime', 'time') NOT NULL, -- 🔥 Define el tipo de dato real
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

--  Crear tabla de usuarios
CREATE TABLE usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    uuid_usuario CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()), --  Para sincronización en la nube
    id_empresa INT NOT NULL, --  Cada usuario pertenece a UNA empresa
    id_rol INT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL, --  Nombre de usuario único para login
    dni VARCHAR(20) NOT NULL, --  Puede repetirse en distintas empresas
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL, --  Hasheado con SHA2()
    intentos_fallidos INT DEFAULT 0, --  Manejo de bloqueo por intentos fallidos
    bloqueado ENUM('no', 'si') DEFAULT 'no',
    id_pregunta INT NOT NULL, --  Relación con la tabla `parametro`
    respuesta_secreta VARBINARY(255) NOT NULL, --  Encriptada con AES-256
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa) ON DELETE CASCADE,
    FOREIGN KEY (id_rol) REFERENCES rol(id_rol) ON DELETE RESTRICT,
    FOREIGN KEY (id_pregunta) REFERENCES parametro(id_parametro) ON DELETE RESTRICT
);

--  Crear tabla de sesiones de usuario
/*
CREATE TABLE `sesion` (
  `id_sesion` INT NOT NULL AUTO_INCREMENT,
  `id_usuario` INT NOT NULL,
  `token` VARCHAR(512) NOT NULL,
  `ip` VARCHAR(45) NOT NULL,
  `user_agent` TEXT,
  `inicio` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fecha_expiracion` TIMESTAMP NOT NULL, -- expiración del token
  `estado` ENUM('activa','expirada','cerrada') DEFAULT 'activa',
  PRIMARY KEY (`id_sesion`),
  UNIQUE KEY `token` (`token`),
  KEY `id_usuario` (`id_usuario`),
  KEY `estado` (`estado`), -- 🔹 Índice para mejorar búsqueda de sesiones activas
  CONSTRAINT `sesion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
*/

--  Crear tabla de auditoría con registro completo de llamadas a la API
CREATE TABLE auditoria (
    id_auditoria INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    metodo VARCHAR(10) NOT NULL, --  Método HTTP (GET, POST, PUT, DELETE)
    endpoint VARCHAR(255) NOT NULL, --  Endpoint llamado
    ip VARCHAR(45) NOT NULL, --  Dirección IP del usuario
    user_agent TEXT, --  Información del dispositivo/navegador
    entrada JSON NOT NULL, --  Parámetros recibidos en la solicitud
    salida JSON NOT NULL, --  Respuesta enviada al usuario
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP, --  Fecha del evento
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

CREATE TABLE lista_negra_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(512) NOT NULL,
    revocado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (token) -- Evita duplicados
);


--  Reactivar la integridad referencial
SET FOREIGN_KEY_CHECKS = 1;
