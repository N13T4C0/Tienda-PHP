<h1>Gestion de categorias</h1>

<div class="layout-tienda" style="grid-template-columns:1fr 320px;">

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
                        <td class="txt-centro acciones" style="justify-content:center;">
                            <a class="boton boton-pequeno boton-borrar"
                               href="<?= URL_BASE ?>/admin/borrarCategoria/<?= $c['id'] ?>"
                               onclick="return confirm('¿Borrar esta categoria?')">Borrar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p style="margin-top:1rem;"><a href="<?= URL_BASE ?>/admin">&larr; Volver al panel</a></p>
    </div>

    <aside class="formulario" style="margin:0;max-width:none;">
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
            <button class="boton" type="submit" style="width:100%;">Crear</button>
        </form>
    </aside>

</div>
