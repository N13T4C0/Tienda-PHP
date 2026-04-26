<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>Detalle del Producto</h1>
    
    <div class="producto-detalle">
        <p>Vista de detalle del producto ID: <?= htmlspecialchars($id ?? 'N/A') ?></p>
        <p>Próximamente se mostrará la información completa del producto.</p>
    </div>
    
    <a href="<?= $base_url ?>/productos" class="btn">Volver a Productos</a>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
