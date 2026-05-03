<?php
namespace Servicios;

use Repositorios\CategoriaRepositorio;

/**
 * CategoriaServicio
 *
 * Logica de negocio relacionada con categorias.
 */
class CategoriaServicio
{
    private CategoriaRepositorio $repositorio;

    public function __construct()
    {
        $this->repositorio = new CategoriaRepositorio();
    }

    /**
     * Devuelve todas las categorias.
     */
    public function listarTodas(): array
    {
        return $this->repositorio->obtenerTodas();
    }

    /**
     * Devuelve una categoria por su id.
     */
    public function obtenerUna(int $id): ?array
    {
        return $this->repositorio->obtenerUna($id);
    }

    /**
     * Crea una categoria nueva.
     */
    public function crear(array $datos): int
    {
        return $this->repositorio->insertar($datos);
    }

    /**
     * Modifica una categoria existente.
     */
    public function modificar(int $id, array $datos): bool
    {
        return $this->repositorio->actualizar($id, $datos);
    }

    /**
     * Elimina una categoria. Lanza una excepcion si tiene productos asociados.
     *
     * @throws \RuntimeException
     */
    public function eliminar(int $id): bool
    {
        if ($this->repositorio->tieneProductos($id)) {
            throw new \RuntimeException('No se puede borrar: la categoria tiene productos');
        }

        return $this->repositorio->eliminar($id);
    }
}
