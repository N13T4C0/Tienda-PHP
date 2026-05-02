-- =====================================================
--  Base de datos: tiendaphp
--  Proyecto MVC sencillo de tienda online (2º DAW)
-- =====================================================

DROP DATABASE IF EXISTS tiendaphp;
CREATE DATABASE tiendaphp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tiendaphp;

-- -----------------------------------------------------
-- Tabla: categorias
-- -----------------------------------------------------
CREATE TABLE categorias (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    fecha_alta  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: productos
-- -----------------------------------------------------
CREATE TABLE productos (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id  INT NOT NULL,
    nombre        VARCHAR(150) NOT NULL,
    descripcion   TEXT,
    precio        DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock         INT NOT NULL DEFAULT 0,
    imagen        VARCHAR(255) DEFAULT 'sin-imagen.svg',
    visible       TINYINT(1) NOT NULL DEFAULT 1,
    fecha_alta    DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: usuarios
-- -----------------------------------------------------
CREATE TABLE usuarios (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(80) NOT NULL,
    apellidos    VARCHAR(120),
    email        VARCHAR(150) NOT NULL UNIQUE,
    clave        VARCHAR(255) NOT NULL,
    rol          ENUM('cliente','admin') NOT NULL DEFAULT 'cliente',
    activado     TINYINT(1) NOT NULL DEFAULT 0,
    token_email  VARCHAR(64) DEFAULT NULL,
    fecha_alta   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: pedidos
-- -----------------------------------------------------
CREATE TABLE pedidos (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id    INT NOT NULL,
    importe_total DECIMAL(10,2) NOT NULL DEFAULT 0,
    direccion     VARCHAR(255),
    localidad     VARCHAR(120),
    provincia     VARCHAR(120),
    estado        ENUM('pendiente','enviado','entregado','cancelado') NOT NULL DEFAULT 'pendiente',
    fecha_pedido  DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedido_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: lineas_pedido
-- -----------------------------------------------------
CREATE TABLE lineas_pedido (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id       INT NOT NULL,
    producto_id     INT NOT NULL,
    nombre_producto VARCHAR(150) NOT NULL,
    precio_unidad   DECIMAL(10,2) NOT NULL,
    unidades        INT NOT NULL,
    subtotal        DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_linea_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_linea_producto
        FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
--  Datos de prueba
-- =====================================================

INSERT INTO categorias (nombre, descripcion) VALUES
    ('Electronica',  'Dispositivos electronicos y gadgets'),
    ('Ropa',         'Ropa y complementos'),
    ('Hogar',        'Articulos para el hogar'),
    ('Libros',       'Libros y revistas');
-- cambniar precio de los productos para probar el paypal, NO PASARSE DE 500
INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, imagen) VALUES
    (1, 'Auriculares Bluetooth', 'Auriculares inalambricos con cancelacion de ruido', 1, 25, 'auriculares.jpg'),
    (1, 'Smartwatch Sport',      'Reloj inteligente resistente al agua',              1, 15, 'smartwatch.jpg'),
    (1, 'Teclado mecanico',      'Teclado mecanico retroiluminado RGB',               1, 10, 'teclado.jpg'),
    (2, 'Camiseta basica',       'Camiseta 100% algodon, varios colores',              1, 50, 'camiseta.jpg'),
    (2, 'Sudadera con capucha',  'Sudadera comoda y abrigada',                        1, 30, 'sudadera.jpg'),
    (3, 'Lampara de mesa LED',   'Lampara con luz regulable',                         1, 20, 'lampara.jpg'),
    (3, 'Juego de sabanas',      'Sabanas de microfibra cama 90',                     1, 12, 'sabanas.jpg'),
    (4, 'Aprende PHP en 30 dias','Manual practico para aprender PHP',                 1,  8, 'libro-php.jpg'),
    (4, 'Bases de datos MySQL',  'Guia completa de MySQL',                            1,  6, 'libro-mysql.jpg');

ALTER TABLE usuarios ADD COLUMN google_id VARCHAR(255) DEFAULT NULL UNIQUE;
ALTER TABLE usuarios ADD COLUMN avatar VARCHAR(255) DEFAULT NULL;
-- IMPORTANTE: los usuarios de prueba NO se crean aqui,
-- ya que las contrasenas deben hashearse con password_hash (bcrypt).
-- Ejecuta UNA vez el script:
--    http://localhost/ProyectoTiendaPHP/public/instalar.php
-- Crea automaticamente:
--    admin@tiendaphp.com   / clave: admin123    (rol admin)
--    cliente@tiendaphp.com / clave: cliente123  (rol cliente)
