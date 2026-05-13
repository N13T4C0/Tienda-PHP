<?php

namespace Servicios;

use Repositorios\CategoriaRepositorio;
use Modelos\Categoria;

class CategoriaServicio
{
    private CategoriaRepositorio $repositorio;

    public function __construct()
    {
        $this->repositorio = new CategoriaRepositorio();
    }

    public function listarTodas(): array
    {
        return $this->repositorio->obtenerTodas();
    }

    public function obtenerUna(int $id): ?Categoria
    {
        return $this->repositorio->obtenerUna($id);
    }

    public function contarTotales(): int
    {
        return $this->repositorio->contarTodas();
    }

    public function crear(array $datos): int
    {
        return $this->repositorio->insertar($datos);
    }

    public function modificar(int $id, array $datos): bool
    {
        return $this->repositorio->actualizar($id, $datos);
    }

    public function eliminar(int $id): bool
    {
        if ($this->repositorio->tieneProductos($id)) {
            // Esta excepción la capturará el controlador para mostrar un mensaje de error
            throw new \RuntimeException('No se puede borrar la categoría porque tiene productos asociados.');
        }
        return $this->repositorio->eliminar($id);
    }
}
