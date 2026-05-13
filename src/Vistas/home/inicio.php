<section class="hero">
    <h1>Bienvenido a netStore</h1>
    <p class="hero__subtitulo">
        Tu tienda online de confianza para electrónica, ropa, hogar y más.
    </p>
    <a class="boton hero__cta" href="<?= URL_BASE ?>/producto">Ver catálogo</a>
</section>

<h2>Categorías</h2>
<div class="rejilla-productos">
    <?php foreach ($categorias as $cat): ?>
        <a class="tarjeta-producto tarjeta-producto--categoria" href="<?= URL_BASE ?>/producto/index/<?= $cat->id ?>">
            <h3><?= htmlspecialchars($cat->nombre) ?></h3>
            <p class="tarjeta-producto__descripcion"><?= htmlspecialchars($cat->descripcion ?? '') ?></p>
        </a>
    <?php endforeach; ?>
</div>

<h2 class="seccion__titulo">Productos destacados</h2>
<div class="rejilla-productos">
    <?php if (empty($destacados)): ?>
        <p>No hay productos disponibles en este momento.</p>
    <?php else: ?>
        <?php foreach ($destacados as $p): ?>
            <article class="tarjeta-producto">
                <?php
                // Verificamos la imagen usando la propiedad del objeto
                $nombreImagen = $p->imagen ?? 'sin-imagen.svg';
                $rutaImg = file_exists(PUBLICO . '/uploads/imagenes/' . $nombreImagen)
                    ? URL_BASE . '/uploads/imagenes/' . htmlspecialchars($nombreImagen)
                    : URL_BASE . '/img/' . htmlspecialchars($nombreImagen);
                ?>
                <img src="<?= $rutaImg ?>"
                    alt="<?= htmlspecialchars($p->nombre) ?>"
                    onerror="this.src='<?= URL_BASE ?>/img/sin-imagen.svg'">

                <div class="cuerpo">
                    <span class="etiqueta-categoria"><?= htmlspecialchars($p->categoria_nombre ?? 'General') ?></span>
                    <h3 class="tarjeta-producto__nombre"><?= htmlspecialchars($p->nombre) ?></h3>
                    <p class="precio"><?= number_format($p->precio, 2) ?> &euro;</p>
                    <div class="pie">
                        <a class="boton boton-pequeno" href="<?= URL_BASE ?>/producto/detalle/<?= $p->id ?>">Ver detalle</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>