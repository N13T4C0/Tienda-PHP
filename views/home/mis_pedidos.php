<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>Mis Pedidos</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="pedidos-list">
        <p>No tienes pedidos realizados aún.</p>
    </div>
    
    <a href="<?= $base_url ?>/productos" class="btn">Ver Productos</a>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
