<h1>Catálogo</h1>

<div class="layout-tienda">

    <aside class="barra-lateral">
        <h3>Categorías</h3>
        <ul>
            <li>
                <a class="<?= $categoriaActiva === null ? 'activo' : '' ?>"
                    href="<?= URL_BASE ?>/producto">Todas</a>
            </li>
            <?php foreach ($categorias as $cat): ?>
                <li>
                    <a class="<?= $categoriaActiva === (int) $cat->id ? 'activo' : '' ?>"
                        href="<?= URL_BASE ?>/producto/index/<?= $cat->id ?>">
                        <?= htmlspecialchars($cat->nombre) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <section>
        <?php if (empty($productos)): ?>
            <div class="bloque-vacio">No hay productos en esta categoría.</div>
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
                            <h3 class="tarjeta-producto__nombre">
                                <a href="<?= URL_BASE ?>/producto/detalle/<?= $p->id ?>">
                                    <?= htmlspecialchars($p->nombre) ?>
                                </a>
                            </h3>
                            <p class="precio"><?= number_format($p->precio, 2) ?> &euro;</p>
                            <div class="pie">
                                <a class="boton boton-pequeno" href="<?= URL_BASE ?>/producto/detalle/<?= $p->id ?>">
                                    Ver detalle
                                </a>
                                <?php if ((int) $p->stock > 0): ?>
                                    <form action="<?= URL_BASE ?>/cesta/anadir" method="POST">
                                        <input type="hidden" name="id_producto" value="<?= $p->id ?>">
                                        <input type="hidden" name="cantidad" value="1">
                                        <button type="submit" class="boton boton-pequeno boton-secundario">
                                            + Cesta
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="sin-stock">Sin stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($paginador->totalPaginas() > 1): ?>
                <?php
                $urlBase = URL_BASE . ($categoriaActiva ? '/producto/index/' . $categoriaActiva : '/producto');
                ?>
                <nav class="paginacion">
                    <?php if ($paginador->hayAnterior()): ?>
                        <a class="boton boton-pequeno boton-secundario"
                            href="<?= $urlBase ?>?pagina=<?= $paginador->paginaActual() - 1 ?>">
                            &laquo; Anterior
                        </a>
                    <?php endif; ?>

                    <span class="paginacion__info">
                        Página <?= $paginador->paginaActual() ?> de <?= $paginador->totalPaginas() ?>
                    </span>

                    <?php if ($paginador->haySiguiente()): ?>
                        <a class="boton boton-pequeno boton-secundario"
                            href="<?= $urlBase ?>?pagina=<?= $paginador->paginaActual() + 1 ?>">
                            Siguiente &raquo;
                        </a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>

        <?php endif; ?>
    </section>

</div>