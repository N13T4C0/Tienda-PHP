<?php

namespace Servicios;

use Repositorios\ProductoRepositorio;

class ProductoServicio
{
    private $repo;

    public function __construct()
    {
        $this->repo = new ProductoRepositorio();
    }

    public function contarTotales(): int
    {
        return $this->repo->contarTodos();
    }

    public function listarTodos(?int $idCategoria = null, ?string $texto = null): array
    {
        if ($texto !== null && $texto !== '') {
            return $this->repo->buscarPorTexto($texto);
        }
        if ($idCategoria !== null) {
            return $this->repo->obtenerPorCategoria($idCategoria);
        }
        return $this->repo->obtenerVisibles();
    }

    public function listarTodosAdmin(): array
    {
        return $this->repo->obtenerTodos();
    }

    public function obtenerUno(int $id): ?object
    {
        return $this->repo->buscarPorId($id);
    }

    public function crear(array $datos): bool
    {
        return $this->repo->guardar($datos);
    }

    public function modificar(int $id, array $datos): bool
    {
        return $this->repo->actualizar($id, $datos);
    }

    public function eliminar(int $id): bool
    {
        return $this->repo->borrarLogico($id);
    }

    public function restaurar(int $id): bool
    {
        return $this->repo->restaurar($id);
    }

    public function descontarStock(int $id, int $cantidad): void
    {
        $this->repo->descontarStock($id, $cantidad);
    }
}
