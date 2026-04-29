<div style="margin-bottom:1rem;">
    <a href="<?= URL_BASE ?>/producto">&larr; Volver al catalogo</a>
</div>

<article class="detalle-producto">
    <img src="<?= URL_BASE ?>/img/<?= htmlspecialchars($producto['imagen']) ?>"
         alt="<?= htmlspecialchars($producto['nombre']) ?>"
         onerror="this.src='<?= URL_BASE ?>/img/sin-imagen.svg'">

    <div>
        <span class="etiqueta-categoria"><?= htmlspecialchars($producto['categoria_nombre']) ?></span>
        <h1 style="margin-top:.5rem;"><?= htmlspecialchars($producto['nombre']) ?></h1>
        <p style="margin-top:1rem;color:#555;">
            <?= nl2br(htmlspecialchars($producto['descripcion'])) ?>
        </p>

        <p class="precio" style="font-size:2rem;color:var(--color-acento);font-weight:bold;margin-top:1.25rem;">
            <?= number_format($producto['precio'], 2) ?> &euro;
        </p>

        <p style="margin-bottom:1rem;">
            <?php if ((int) $producto['stock'] > 0): ?>
                <strong>Stock:</strong> <?= $producto['stock'] ?> unidades disponibles
            <?php else: ?>
                <strong style="color:var(--color-error);">Producto sin stock</strong>
            <?php endif; ?>
        </p>

        <?php if ((int) $producto['stock'] > 0): ?>
            <form method="POST" action="<?= URL_BASE ?>/cesta/anadir" style="display:flex;gap:.5rem;align-items:center;">
                <input type="hidden" name="id_producto" value="<?= $producto['id'] ?>">
                <input type="number" name="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>"
                       class="input-cantidad">
                <button type="submit" class="boton">Anadir a la cesta</button>
            </form>
        <?php endif; ?>
    </div>
</article>
