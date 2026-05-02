<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="<?= URL_BASE ?>/css/estilo.css">
</head>

<section class="hero">
    <h1>Bienvenido a TiendaPHP</h1>
    <p class="hero__subtitulo">
        Tu tienda online de confianza para electronica, ropa, hogar y mas.
    </p>
    <a class="boton hero__cta" href="<?= URL_BASE ?>/producto">Ver catalogo</a>
</section>

<h2>Categorias</h2>
<div class="rejilla-productos">
    <?php foreach ($categorias as $cat): ?>
        <a class="tarjeta-producto tarjeta-producto--categoria" href="<?= URL_BASE ?>/producto/index/<?= $cat['id'] ?>">
            <h3><?= htmlspecialchars($cat['nombre']) ?></h3>
            <p class="tarjeta-producto__descripcion"><?= htmlspecialchars($cat['descripcion']) ?></p>
        </a>
    <?php endforeach; ?>
</div>

<h2 class="seccion__titulo">Productos destacados</h2>
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
                <h3 class="tarjeta-producto__nombre"><?= htmlspecialchars($p['nombre']) ?></h3>
                <p class="precio"><?= number_format($p['precio'], 2) ?> &euro;</p>
                <div class="pie">
                    <a class="boton boton-pequeno" href="<?= URL_BASE ?>/producto/detalle/<?= $p['id'] ?>">Ver</a>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</div>
