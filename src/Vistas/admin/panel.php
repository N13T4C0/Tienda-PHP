<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/css/estilo.css">
</head>

<h1>Panel de administracion</h1>
<p>Hola <strong><?= htmlspecialchars(Sesion::usuario()['nombre']) ?></strong>, gestiona tu tienda desde aqui.</p>

<div class="rejilla-productos rejilla-productos--panel">
    <a class="tarjeta-producto tarjeta-producto--stat" href="<?= URL_BASE ?>/admin/productos">
        <h2><?= $totalProductos ?></h2>
        <p>Productos</p>
    </a>

    <a class="tarjeta-producto tarjeta-producto--stat" href="<?= URL_BASE ?>/admin/categorias">
        <h2><?= $totalCategorias ?></h2>
        <p>Categorias</p>
    </a>

    <a class="tarjeta-producto tarjeta-producto--stat" href="<?= URL_BASE ?>/admin/usuarios">
        <h2><?= $totalUsuarios ?></h2>
        <p>Usuarios</p>
    </a>
</div>