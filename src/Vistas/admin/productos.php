<div class="cabecera-seccion">
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
            <tr>
                <td colspan="7" class="txt-centro">No hay productos.</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['categoria_nombre']) ?></td>
                <td class="txt-derecha"><?= number_format($p['precio'], 2) ?> &euro;</td>
                <td class="txt-centro"><?= $p['stock'] ?></td>
                <td class="txt-centro"><?= $p['visible'] ? 'Si' : 'No' ?></td>
                <td class="txt-centro acciones acciones--centro">
                    <a class="boton boton-pequeno boton-secundario"
                        href="<?= URL_BASE ?>/admin/editarProducto/<?= $p['id'] ?>">Editar</a>

                    <?php if ($p['visible']): ?>
                        <a class="boton boton-pequeno boton-borrar"
                            href="<?= URL_BASE ?>/admin/borrarProducto/<?= $p['id'] ?>"
                            onclick="return confirm('¿Borrar este producto?')">Borrar</a>
                    <?php else: ?>
                        <a class="boton boton-pequeno boton-secundario"
                            href="<?= URL_BASE ?>/admin/restaurarProducto/<?= $p['id'] ?>"
                            onclick="return confirm('¿Restaurar este producto?')">Restaurar</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php // &larr; es la en HTML para lña  flecha izquierda ← 
?>
<p class="volver">
    <a href="<?= URL_BASE ?>/admin">&larr; Volver al panel</a>
</p>