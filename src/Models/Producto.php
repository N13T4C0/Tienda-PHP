<?php
namespace App\Models;

class Producto {
    public $id;
    public $categoria_id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $precio_oferta;
    public $stock;
    public $activo;
    public $imagen;
    public $created_at;
    public $updated_at;
    
    // Relación con categoría
    public $categoria_nombre;
}