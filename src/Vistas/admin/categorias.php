<h1>Gestión de categorías</h1>

<div class="layout-tienda layout-tienda--checkout">
    <div>
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="txt-centro">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categorias)): ?>
                    <tr>
                        <td colspan="4" class="txt-centro">No hay categorías.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($categorias as $c): ?>
                    <tr>
                        <td><?= $c->id ?></td>
                        <td><?= htmlspecialchars($c->nombre) ?></td>
                        <td><?= htmlspecialchars($c->descripcion) ?></td>
                        <td class="txt-centro acciones acciones--centro">
                            <a class="boton boton-pequeno boton-secundario"
                                href="<?= URL_BASE ?>/admin/editarCategoria/<?= $c->id ?>">Editar</a>
                            <a class="boton boton-pequeno boton-borrar"
                                href="<?= URL_BASE ?>/admin/borrarCategoria/<?= $c->id ?>"
                                onclick="return confirm('¿Borrar esta categoría?')">Borrar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="volver"><a href="<?= URL_BASE ?>/admin">&larr; Volver al panel</a></p>
    </div>

    <aside class="formulario formulario--checkout">
        <?php if (!empty($categoriaEditar)): ?>
            <h2>Editar categoría</h2>
            <form method="POST" action="<?= URL_BASE ?>/admin/guardarCategoria">
                <input type="hidden" name="id" value="<?= $categoriaEditar->id ?>">
                <div class="campo">
                    <label for="nombre">Nombre *</label>
                    <input type="text" name="nombre" id="nombre" required
                        value="<?= htmlspecialchars($categoriaEditar->nombre) ?>">
                </div>
                <div class="campo">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion"><?= htmlspecialchars($categoriaEditar->descripcion) ?></textarea>
                </div>
                <div class="acciones">
                    <button class="boton" type="submit">Guardar</button>
                    <a class="boton boton-secundario" href="<?= URL_BASE ?>/admin/categorias">Cancelar</a>
                </div>
            </form>
        <?php else: ?>
            <h2>Nueva categoría</h2>
            <form method="POST" action="<?= URL_BASE ?>/admin/guardarCategoria">
                <div class="campo">
                    <label for="nombre">Nombre *</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>
                <div class="campo">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion"></textarea>
                </div>
                <button class="boton boton--full" type="submit">Crear</button>
            </form>
        <?php endif; ?>
    </aside>
</div>