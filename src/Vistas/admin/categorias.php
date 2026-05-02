<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/css/estilo.css">
</head>

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
    </aside>

</div>