<?php

use Lib\Sesion; ?>

<div class="cabecera-seccion">
    <h1>Panel de administración</h1>
</div>

<p>Hola <strong><?= htmlspecialchars(Sesion::usuario()['nombre']) ?></strong>, gestiona tu tienda desde aquí.</p>

<div class="rejilla-stats">
    <a class="tarjeta-stat" href="<?= URL_BASE ?>/admin/productos">
        <h2><?= $totalProductos ?></h2>
        <p>Productos</p>
    </a>
    <a class="tarjeta-stat" href="<?= URL_BASE ?>/admin/categorias">
        <h2><?= $totalCategorias ?></h2>
        <p>Categorías</p>
    </a>
    <a class="tarjeta-stat" href="<?= URL_BASE ?>/admin/usuarios">
        <h2><?= $totalUsuarios ?></h2>
        <p>Usuarios</p>
    </a>
</div>