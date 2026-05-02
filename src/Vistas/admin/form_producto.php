<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/css/estilo.css">
</head>

<div class="formulario ancho">
    <h1><?= $producto ? 'Editar producto' : 'Nuevo producto' ?></h1>

    <form method="POST" action="<?= URL_BASE ?>/admin/guardarProducto">
        <?php if ($producto): ?>
            <input type="hidden" name="id" value="<?= $producto['id'] ?>">
        <?php endif; ?>

        <div class="campo">
            <label for="nombre">Nombre *</label>
            <input type="text" name="nombre" id="nombre" required
                   value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="categoria_id">Categoria *</label>
            <select name="categoria_id" id="categoria_id" required>
                <option value="">-- Selecciona --</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= $c['id'] ?>"
                        <?= ($producto && (int) $producto['categoria_id'] === (int) $c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="descripcion">Descripcion</label>
            <textarea name="descripcion" id="descripcion"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="campo campo--doble">
            <div class="campo">
                <label for="precio">Precio (&euro;) *</label>
                <input type="number" step="0.01" min="0" name="precio" id="precio" required
                       value="<?= htmlspecialchars($producto['precio'] ?? '0') ?>">
            </div>

            <div class="campo">
                <label for="stock">Stock</label>
                <input type="number" min="0" name="stock" id="stock"
                       value="<?= htmlspecialchars($producto['stock'] ?? '0') ?>">
            </div>
        </div>

        <div class="campo">
            <label for="imagen">Nombre del archivo de imagen
                <small>(en /public/img)</small>
            </label>
            <input type="text" name="imagen" id="imagen"
                   value="<?= htmlspecialchars($producto['imagen'] ?? 'sin-imagen.svg') ?>">
        </div>

        <div class="campo">
            <label>
                <input type="checkbox" name="visible" value="1"
                       <?= (!$producto || $producto['visible']) ? 'checked' : '' ?>>
                Visible en la tienda
            </label>
        </div>

        <div class="acciones">
            <button class="boton" type="submit">Guardar</button>
            <a class="boton boton-secundario" href="<?= URL_BASE ?>/admin/productos">Cancelar</a>
        </div>
    </form>
</div>