<div class="volver">
    <a href="<?= URL_BASE ?>/producto">&larr; Volver al catalogo</a>
</div>

<article class="detalle-producto">
    <?php
    $rutaImg = file_exists(PUBLICO . '/uploads/imagenes/' . $producto['imagen'])
        ? URL_BASE . '/uploads/imagenes/' . htmlspecialchars($producto['imagen'])
        : URL_BASE . '/img/' . htmlspecialchars($producto['imagen']);
    ?>
    <img src="<?= $rutaImg ?>"
         alt="<?= htmlspecialchars($producto['nombre']) ?>"
         onerror="this.src='<?= URL_BASE ?>/img/sin-imagen.svg'">

    <div>
        <span class="etiqueta-categoria"><?= htmlspecialchars($producto['categoria_nombre']) ?></span>
        <h1 class="detalle-producto__nombre"><?= htmlspecialchars($producto['nombre']) ?></h1>
        <p class="detalle-producto__descripcion">
            <?php // nl2br convierte los saltos de linea \n del texto en <br> para que se vean en HTML ?>
            <?= nl2br(htmlspecialchars($producto['descripcion'])) ?>
        </p>

        <p class="precio detalle-producto__precio">
            <?= number_format($producto['precio'], 2) ?> &euro;
        </p>

        <p class="detalle-producto__stock">
            <?php if ((int) $producto['stock'] > 0): ?>
                <strong>Stock:</strong> <?= $producto['stock'] ?> unidades disponibles
            <?php else: ?>
                <strong class="detalle-producto__sin-stock">Producto sin stock</strong>
            <?php endif; ?>
        </p>

        <?php if ((int) $producto['stock'] > 0): ?>
            <form method="POST" action="<?= URL_BASE ?>/cesta/anadir" class="form-anadir">
                <?php // type="hidden" envia el id del producto sin mostrarlo al usuario ?>
                <input type="hidden" name="id_producto" value="<?= $producto['id'] ?>">
                <input type="number" name="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>"
                       class="input-cantidad">
                <button type="submit" class="boton">Anadir a la cesta</button>
            </form>
        <?php endif; ?>
    </div>
</article>