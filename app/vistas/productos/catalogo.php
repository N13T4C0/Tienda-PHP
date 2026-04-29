<h1>Catalogo</h1>

<div class="layout-tienda">

    <aside class="barra-lateral">
        <h3>Categorias</h3>
        <ul>
            <li>
                <a class="<?= $categoriaActiva === null ? 'activo' : '' ?>"
                   href="<?= URL_BASE ?>/producto">Todas</a>
            </li>
            <?php foreach ($categorias as $cat): ?>
                <li>
                    <a class="<?= $categoriaActiva === (int) $cat['id'] ? 'activo' : '' ?>"
                       href="<?= URL_BASE ?>/producto/index/<?= $cat['id'] ?>">
                        <?= htmlspecialchars($cat['nombre']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <section>
        <?php if (empty($productos)): ?>
            <div class="bloque-vacio">No hay productos en esta categoria.</div>
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
                            <div class="pie">
                                <a class="boton boton-pequeno" href="<?= URL_BASE ?>/producto/detalle/<?= $p['id'] ?>">
                                    Ver detalle
                                </a>
                                <?php if ((int) $p['stock'] > 0): ?>
                                    <form method="POST" action="<?= URL_BASE ?>/cesta/anadir" style="display:inline;">
                                        <input type="hidden" name="id_producto" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="cantidad"    value="1">
                                        <button class="boton boton-pequeno boton-secundario" type="submit">+ Cesta</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color:var(--color-error);">Sin stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</div>
