<?php

namespace Core;

use Config\Conexion;
use PDO;

abstract class BaseRepositorio
{
    protected PDO $bd;

    public function __construct()
    {
        $this->bd = Conexion::abrir();
    }
}
