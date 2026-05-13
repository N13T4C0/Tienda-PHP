
<h1>Gestion de categorias</h1>

<div class="layout-tienda layout-tienda--checkout">

    <div>
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th class="txt-centro">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categorias)): ?>
                    <tr><td colspan="4" class="txt-centro">No hay categorias.</td></tr>
                <?php endif; ?>
                <?php foreach ($categorias as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                        <td><?= htmlspecialchars($c['descripcion']) ?></td>
                        <td class="txt-centro acciones acciones--centro">
                            <a class="boton boton-pequeno boton-editar"
                               href="<?= URL_BASE ?>/admin/editarCategoria/<?= $c['id'] ?>">Editar</a>
                            <a class="boton boton-pequeno boton-borrar"
                               href="<?= URL_BASE ?>/admin/borrarCategoria/<?= $c['id'] ?>"
                               onclick="return confirm('¿Borrar esta categoria?')">Borrar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="volver"><a href="<?= URL_BASE ?>/admin">&larr; Volver al panel</a></p>
    </div>

    <aside class="formulario formulario--checkout">

        <?php if ($catEditar): ?>
            <!-- MODO EDICION: formulario pre-relleno con los datos de la categoria -->
            <h2>Editar categoria</h2>
            <form method="POST" action="<?= URL_BASE ?>/admin/guardarCategoria">
                <!-- Campo oculto con el id: indica al controlador que es una edicion -->
                <input type="hidden" name="id" value="<?= $catEditar['id'] ?>">
                <div class="campo">
                    <label for="nombre">Nombre *</label>
                    <input type="text" name="nombre" id="nombre" required
                           value="<?= htmlspecialchars($catEditar['nombre']) ?>">
                </div>
                <div class="campo">
                    <label for="descripcion">Descripcion</label>
                    <textarea name="descripcion" id="descripcion"><?= htmlspecialchars($catEditar['descripcion']) ?></textarea>
                </div>
                <div style="display:flex; gap:.5rem;">
                    <button class="boton boton--full" type="submit">Guardar cambios</button>
                    <a class="boton boton--full boton--secundario"
                       href="<?= URL_BASE ?>/admin/categorias">Cancelar</a>
                </div>
            </form>

        <?php else: ?>
            <!-- MODO CREACION: formulario vacio para una categoria nueva -->
            <h2>Nueva categoria</h2>
            <form method="POST" action="<?= URL_BASE ?>/admin/guardarCategoria">
                <div class="campo">
                    <label for="nombre">Nombre *</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>
                <div class="campo">
                    <label for="descripcion">Descripcion</label>
                    <textarea name="descripcion" id="descripcion"></textarea>
                </div>
                <button class="boton boton--full" type="submit">Crear</button>
            </form>

        <?php endif; ?>

    </aside>

</div>
