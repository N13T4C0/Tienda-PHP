<h1>Resultados de busqueda</h1>
<p style="margin-bottom:1.5rem;">Buscando: <strong><?= htmlspecialchars($texto) ?></strong></p>

<?php if ($texto === ''): ?>
    <div class="bloque-vacio">Escribe algo en la barra de busqueda.</div>
<?php elseif (empty($productos)): ?>
    <div class="bloque-vacio">No se encontraron productos para "<?= htmlspecialchars($texto) ?>".</div>
<?php else: ?>
    <div class="rejilla-productos">
        <?php foreach ($productos as $p): ?>
            <article class="tarjeta-producto">
                <a href="<?= URL_BASE ?>/producto/detalle/<?= $p['id'] ?>">
                    <img src="<?= URL_BASE ?>/img/<?= htmlspecialchars($p['imagen']) ?>"
                         alt="<?= htmlspecialchars($p['nombre']) ?>"
                         onerror="this.src='<?= URL_BASE ?>/img/sin-imagen.svg'">
                </a>
                <div class="cuerpo">
                    <span class="etiqueta-categoria"><?= htmlspecialchars($p['categoria_nombre']) ?></span>
                    <h3 style="margin-top:.5rem;">
                        <a href="<?= URL_BASE ?>/producto/detalle/<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['nombre']) ?>
                        </a>
                    </h3>
                    <p class="precio"><?= number_format($p['precio'], 2) ?> &euro;</p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
