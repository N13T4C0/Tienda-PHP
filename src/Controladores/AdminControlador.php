<?php

namespace Controladores;

use Lib\Pagina;
use Lib\Sesion;
use Servicios\ProductoServicio;
use Servicios\CategoriaServicio;
use Servicios\UsuarioServicio;

class AdminControlador
{
    private ProductoServicio $prodServ;
    private CategoriaServicio $catServ;
    private UsuarioServicio $userServ;

    public function __construct()
    {
        if (!Sesion::esAdmin()) Sesion::redirigir('');
        $this->prodServ = new ProductoServicio();
        $this->catServ  = new CategoriaServicio();
        $this->userServ = new UsuarioServicio();
    }

    public function index(): void
    {
        Pagina::renderizar('admin/panel', [
            'totalProductos'  => $this->prodServ->contarTotales(),
            'totalCategorias' => $this->catServ->contarTotales(),
            'totalUsuarios'   => $this->userServ->contarTotales(),
        ]);
    }

    public function productos(): void
    {
        Pagina::renderizar('admin/productos', [
            'productos' => $this->prodServ->listarTodosAdmin(),
        ]);
    }

    public function nuevoProducto(): void
    {
        Pagina::renderizar('admin/form_producto', [
            'producto'   => null,
            'categorias' => $this->catServ->listarTodas(),
        ]);
    }

    public function editarProducto(int $id): void
    {
        Pagina::renderizar('admin/form_producto', [
            'producto'   => $this->prodServ->obtenerUno($id),
            'categorias' => $this->catServ->listarTodas(),
        ]);
    }

    public function guardarProducto(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $id ? $this->prodServ->modificar((int) $id, $_POST) : $this->prodServ->crear($_POST);
        }
        Sesion::redirigir('admin/productos');
    }

    public function borrarProducto(int $id): void
    {
        $this->prodServ->eliminar($id);
        Sesion::mensaje('ok', 'Producto eliminado');
        Sesion::redirigir('admin/productos');
    }

    public function restaurarProducto(int $id): void
    {
        $this->prodServ->restaurar($id);
        Sesion::mensaje('ok', 'Producto restaurado');
        Sesion::redirigir('admin/productos');
    }

    public function categorias(): void
    {
        Pagina::renderizar('admin/categorias', [
            'categorias' => $this->catServ->listarTodas(),
        ]);
    }

    public function editarCategoria(int $id): void
    {
        Pagina::renderizar('admin/categorias', [
            'categorias'       => $this->catServ->listarTodas(),
            'categoriaEditar'  => $this->catServ->obtenerUna($id),
        ]);
    }

    public function guardarCategoria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $this->catServ->modificar((int) $id, $_POST);
                Sesion::mensaje('ok', 'Categoría actualizada');
            } else {
                $this->catServ->crear($_POST);
                Sesion::mensaje('ok', 'Categoría creada');
            }
        }
        Sesion::redirigir('admin/categorias');
    }

    public function borrarCategoria(int $id): void
    {
        try {
            $this->catServ->eliminar($id);
            Sesion::mensaje('ok', 'Categoría eliminada');
        } catch (\RuntimeException $e) {
            Sesion::mensaje('error', $e->getMessage());
        }
        Sesion::redirigir('admin/categorias');
    }

    public function usuarios(): void
    {
        Pagina::renderizar('admin/usuarios', [
            'usuarios' => $this->userServ->listarTodos(),
        ]);
    }

    public function borrarUsuario(int $id): void
    {
        $this->userServ->eliminar($id);
        Sesion::redirigir('admin/usuarios');
    }
}
