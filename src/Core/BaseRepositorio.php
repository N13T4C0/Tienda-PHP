<?php

namespace Core;

use Config\Conexion;
use PDO;

/** Clase base para todos los Repositorios — inyecta la conexion PDO */
abstract class BaseRepositorio
{
    protected PDO $bd;

    public function __construct()
    {
        $this->bd = Conexion::abrir();
    }
}
