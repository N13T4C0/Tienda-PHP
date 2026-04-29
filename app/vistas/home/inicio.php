<section style="background:#fff;padding:3rem 2rem;border-radius:6px;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,.08);margin-bottom:2rem;">
    <h1>Bienvenido a TiendaPHP</h1>
    <p style="font-size:1.1rem;margin-top:.5rem;color:#666;">
        Tu tienda online de confianza para electronica, ropa, hogar y mas.
    </p>
    <a class="boton" style="margin-top:1.25rem;" href="<?= URL_BASE ?>/producto">Ver catalogo</a>
</section>

<h2>Categorias</h2>
<div class="rejilla-productos">
    <?php foreach ($categorias as $cat): ?>
        <a class="tarjeta-producto" href="<?= URL_BASE ?>/producto/index/<?= $cat['id'] ?>"
           style="text-decoration:none;text-align:center;padding:1.5rem;">
            <h3><?= htmlspecialchars($cat['nombre']) ?></h3>
            <p style="color:#666;font-size:.9rem;"><?= htmlspecialchars($cat['descripcion']) ?></p>
        </a>
    <?php endforeach; ?>
</div>

<h2 style="margin-top:2rem;">Productos destacados</h2>
<div class="rejilla-productos">
    <?php if (empty($destacados)): ?>
        <p>No hay productos disponibles.</p>
    <?php endif; ?>
    <?php foreach ($destacados as $p): ?>
        <article class="tarjeta-producto">
            <img src="<?= URL_BASE ?>/img/<?= htmlspecialchars($p['imagen']) ?>"
                 alt="<?= htmlspecialchars($p['nombre']) ?>"
                 onerror="this.src='<?= URL_BASE ?>/img/sin-imagen.svg'">
            <div class="cuerpo">
                <span class="etiqueta-categoria"><?= htmlspecialchars($p['categoria_nombre']) ?></span>
                <h3 style="margin-top:.5rem;"><?= htmlspecialchars($p['nombre']) ?></h3>
                <p class="precio"><?= number_format($p['precio'], 2) ?> &euro;</p>
                <div class="pie">
                    <a class="boton boton-pequeno" href="<?= URL_BASE ?>/producto/detalle/<?= $p['id'] ?>">Ver</a>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</div>
