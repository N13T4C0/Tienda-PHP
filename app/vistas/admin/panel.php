<h1>Panel de administracion</h1>
<p>Hola <strong><?= htmlspecialchars(Sesion::usuario()['nombre']) ?></strong>, gestiona tu tienda desde aqui.</p>

<div class="rejilla-productos" style="margin-top:1.5rem;">
    <a class="tarjeta-producto" href="<?= URL_BASE ?>/admin/productos"
       style="text-decoration:none;text-align:center;padding:2rem;">
        <h2><?= $totalProductos ?></h2>
        <p>Productos</p>
    </a>

    <a class="tarjeta-producto" href="<?= URL_BASE ?>/admin/categorias"
       style="text-decoration:none;text-align:center;padding:2rem;">
        <h2><?= $totalCategorias ?></h2>
        <p>Categorias</p>
    </a>

    <a class="tarjeta-producto" href="<?= URL_BASE ?>/admin/usuarios"
       style="text-decoration:none;text-align:center;padding:2rem;">
        <h2><?= $totalUsuarios ?></h2>
        <p>Usuarios</p>
    </a>
</div>
