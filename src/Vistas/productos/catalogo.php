<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/css/estilo.css">
</head>


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
                                    <button
                                        class="boton boton-pequeno boton-secundario btn-anadir-cesta"
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
        <?php endif; ?>
    </section>

</div>

<script>
    document.querySelectorAll('.btn-anadir-cesta').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var boton = this;

            var datos = new FormData();
            datos.append('id_producto', id);
            datos.append('cantidad', 1);

            fetch('<?= URL_BASE ?>/cesta/anadir', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: datos
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function(data) {
                    if (data.ok) {
                        boton.textContent = '✓ Añadido';
                        boton.disabled = true;
                        setTimeout(function() {
                            boton.textContent = '+ Cesta';
                            boton.disabled = false;
                        }, 1500);

                        var contador = document.querySelector('.contador-cesta');
                        if (contador) {
                            contador.textContent = data.totalUnidades;
                        }
                    } else {
                        alert(data.mensaje);
                    }
                })
                .catch(function() {
                    alert('Error al añadir el producto');
                });
        });
    });
</script>