CREATE DATABASE ventas_php;

USE ventas_php;

CREATE TABLE productos(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(255) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    compra DECIMAL(8,2) NOT NULL,
    venta DECIMAL(8,2) NOT NULL,
    existencia INT NOT NULL
);

CREATE TABLE usuarios(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    telefono VARCHAR(25) NOT NULL,
    password VARCHAR(255) NOT NULL
);


INSERT INTO usuarios (usuario, nombre, telefono, password) VALUES ("Secundaria 31", "Secundaria_31", "6667771234", "$2y$10$F0uh/CgEVKRnB3Dk1AaxguIiOukRo7gKhBZBHc2mXMlwWB.Fm48Ke");

CREATE TABLE ventas(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME NOT NULL,
    total DECIMAL(9,2) NOT NULL,
    idUsuario BIGINT NOT NULL,
);  

CREATE TABLE productos_ventas(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cantidad INT NOT NULL,
    precio DECIMAL(8,2) NOT NULL,
    idProducto BIGINT NOT NULL,
    idVenta BIGINT NOT NULL
);