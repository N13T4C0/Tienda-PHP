<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>Productos</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="productos-grid">
        <p>Próximamente se mostrarán los productos disponibles.</p>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
