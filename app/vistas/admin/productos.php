<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <h1>Gestion de productos</h1>
    <a class="boton" href="<?= URL_BASE ?>/admin/nuevoProducto">+ Nuevo producto</a>
</div>

<table class="tabla">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Categoria</th>
            <th class="txt-derecha">Precio</th>
            <th class="txt-centro">Stock</th>
            <th class="txt-centro">Visible</th>
            <th class="txt-centro">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($productos)): ?>
            <tr><td colspan="7" class="txt-centro">No hay productos.</td></tr>
        <?php endif; ?>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['categoria_nombre']) ?></td>
                <td class="txt-derecha"><?= number_format($p['precio'], 2) ?> &euro;</td>
                <td class="txt-centro"><?= $p['stock'] ?></td>
                <td class="txt-centro"><?= $p['visible'] ? 'Si' : 'No' ?></td>
                <td class="txt-centro acciones" style="justify-content:center;">
                    <a class="boton boton-pequeno boton-secundario"
                       href="<?= URL_BASE ?>/admin/editarProducto/<?= $p['id'] ?>">Editar</a>
                    <a class="boton boton-pequeno boton-borrar"
                       href="<?= URL_BASE ?>/admin/borrarProducto/<?= $p['id'] ?>"
                       onclick="return confirm('¿Borrar este producto?')">Borrar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p style="margin-top:1rem;"><a href="<?= URL_BASE ?>/admin">&larr; Volver al panel</a></p>
