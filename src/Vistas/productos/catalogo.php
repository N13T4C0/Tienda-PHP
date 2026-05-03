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
                    <article class="tarjeta-producto" id="producto-<?= $p['id'] ?>">
                        <a href="<?= URL_BASE ?>/producto/detalle/<?= $p['id'] ?>">
                            <?php
                            $rutaImg = file_exists(PUBLICO . '/uploads/imagenes/' . $p['imagen'])
                                ? URL_BASE . '/uploads/imagenes/' . htmlspecialchars($p['imagen'])
                                : URL_BASE . '/img/' . htmlspecialchars($p['imagen']);
                            ?>
                            <img src="<?= $rutaImg ?>"
                                alt="<?= htmlspecialchars($p['nombre']) ?>"
                                onerror="this.src='<?= URL_BASE ?>/img/sin-imagen.svg'">
                        </a>
                        <div class="cuerpo">
                            <span class="etiqueta-categoria"><?= htmlspecialchars($p['categoria_nombre']) ?></span>
                            <h3 class="tarjeta-producto__nombre">
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
                                    <button class="boton boton-pequeno boton-secundario btn-anadir-cesta"
                                        data-id="<?= $p['id'] ?>">
                                        + Cesta
                                    </button>
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
                        Pagina <?= $paginador->paginaActual() ?> de <?= $paginador->totalPaginas() ?>
                        (<?= $paginador->totalElementos() ?> productos)
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

<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-anadir-cesta');
        if (!btn) return;

        btn.disabled = true;
        btn.textContent = '...';

        fetch('<?= URL_BASE ?>/cesta/anadir', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'id_producto=' + btn.dataset.id + '&cantidad=1'
            })
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    document.querySelector('.contador-cesta').textContent = data.total;
                    btn.textContent = '✓';
                    setTimeout(() => {
                        btn.textContent = '+ Cesta';
                        btn.disabled = false;
                    }, 1500);
                } else {
                    btn.textContent = '+ Cesta';
                    btn.disabled = false;
                    alert(data.mensaje);
                }
            })
            .catch(() => {
                btn.textContent = '+ Cesta';
                btn.disabled = false;
            });
    });
</script>