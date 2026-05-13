<?php
namespace Servicios;

use Repositorios\ProductoRepositorio;


class ProductoServicio
{
    private ProductoRepositorio $repositorio;

    public function __construct()
    {
        $this->repositorio = new ProductoRepositorio();
    }

    /**
     * Devuelve los productos del catalogo publico.
     * Si se pasa categoria o texto, filtra por esos criterios.
     */
    public function obtenerCatalogo(?int $idCategoria = null, string $busqueda = ''): array
    {
        if ($busqueda !== '') {
            return $this->repositorio->buscarPorTexto($busqueda);
        }

        if ($idCategoria !== null) {
            return $this->repositorio->obtenerPorCategoria($idCategoria);
        }

        return $this->repositorio->obtenerVisibles();
    }

    /**
     * Devuelve un producto por su id.
     */
    public function obtenerUno(int $id): ?array
    {
        return $this->repositorio->obtenerUno($id);
    }

    /**
     * Devuelve todos los productos para el panel admin.
     */
    public function listarTodos(): array
    {
        return $this->repositorio->obtenerTodos();
    }

    /**
     * Crea un producto nuevo.
     */
    public function crear(array $datos): int
    {
        return $this->repositorio->insertar($datos);
    }

    public function restaurar(int $id): bool
    {
        return $this->repositorio->restaurar($id);
    }

    /**
     * Modifica un producto existente.
     */
    public function modificar(int $id, array $datos): bool
    {
        return $this->repositorio->actualizar($id, $datos);
    }

    /**
     * Elimina un producto.
     */
    public function eliminar(int $id): bool
    {
        return $this->repositorio->eliminar($id);
    }
}
