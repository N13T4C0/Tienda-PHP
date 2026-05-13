<div class="volver">
    <a href="<?= URL_BASE ?>/producto">&larr; Volver al catálogo</a>
</div>

<article class="detalle-producto">
    <?php
    $nombreImg = $producto->imagen ?? 'sin-imagen.svg';
    $rutaImg = file_exists(PUBLICO . '/uploads/imagenes/' . $nombreImg)
        ? URL_BASE . '/uploads/imagenes/' . htmlspecialchars($nombreImg)
        : URL_BASE . '/img/' . htmlspecialchars($nombreImg);
    ?>
    <img src="<?= $rutaImg ?>"
        alt="<?= htmlspecialchars($producto->nombre) ?>"
        onerror="this.src='<?= URL_BASE ?>/img/sin-imagen.svg'">

    <div>
        <span class="etiqueta-categoria"><?= htmlspecialchars($producto->categoria_nombre ?? 'General') ?></span>
        <h1 class="detalle-producto__nombre"><?= htmlspecialchars($producto->nombre) ?></h1>
        <p class="detalle-producto__descripcion">
            <?= nl2br(htmlspecialchars($producto->descripcion ?? '')) ?>
        </p>

        <p class="precio detalle-producto__precio">
            <?= number_format($producto->precio, 2) ?> &euro;
        </p>

        <p class="detalle-producto__stock">
            <?php if ((int) $producto->stock > 0): ?>
                <strong>Stock:</strong> <?= $producto->stock ?> unidades disponibles
            <?php else: ?>
                <strong class="detalle-producto__sin-stock">Producto temporalmente sin stock</strong>
            <?php endif; ?>
        </p>

        <?php if ((int) $producto->stock > 0): ?>
            <form method="POST" action="<?= URL_BASE ?>/cesta/anadir" class="form-anadir">
                <input type="hidden" name="id_producto" value="<?= $producto->id ?>">
                <input type="number" name="cantidad" value="1" min="1" max="<?= $producto->stock ?>"
                    class="input-cantidad">
                <button type="submit" class="boton">Añadir a la cesta</button>
            </form>
        <?php endif; ?>
    </div>
</article>