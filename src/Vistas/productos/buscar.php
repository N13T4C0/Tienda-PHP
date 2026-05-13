<h1>Resultados de búsqueda</h1>
<p style="margin-bottom:1.5rem;">Buscando: <strong><?= htmlspecialchars($texto) ?></strong></p>

<?php if ($texto === ''): ?>
    <div class="bloque-vacio">Escribe algo en la barra de búsqueda.</div>
<?php elseif (empty($productos)): ?>
    <div class="bloque-vacio">No se encontraron productos para "<?= htmlspecialchars($texto) ?>".</div>
<?php else: ?>
    <div class="rejilla-productos">
        <?php foreach ($productos as $p): ?>
            <article class="tarjeta-producto">
                <a href="<?= URL_BASE ?>/producto/detalle/<?= $p->id ?>">
                    <?php
                    $nombreImg = $p->imagen ?? 'sin-imagen.svg';
                    $rutaImg = file_exists(PUBLICO . '/uploads/imagenes/' . $nombreImg)
                        ? URL_BASE . '/uploads/imagenes/' . htmlspecialchars($nombreImg)
                        : URL_BASE . '/img/' . htmlspecialchars($nombreImg);
                    ?>
                    <img src="<?= $rutaImg ?>"
                        alt="<?= htmlspecialchars($p->nombre) ?>"
                        onerror="this.src='<?= URL_BASE ?>/img/sin-imagen.svg'">
                </a>
                <div class="cuerpo">
                    <span class="etiqueta-categoria"><?= htmlspecialchars($p->categoria_nombre ?? 'General') ?></span>
                    <h3 style="margin-top:.5rem;">
                        <a href="<?= URL_BASE ?>/producto/detalle/<?= $p->id ?>">
                            <?= htmlspecialchars($p->nombre) ?>
                        </a>
                    </h3>
                    <p class="precio"><?= number_format($p->precio, 2) ?> &euro;</p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>