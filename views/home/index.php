<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>Bienvenido a Tienda Online</h1>
    
    <?php if (isset($_SESSION['identity'])): ?>
        <p>Hola, <strong><?= htmlspecialchars($_SESSION['identity']->nombre) ?> 
           <?= htmlspecialchars($_SESSION['identity']->apellidos) ?></strong></p>
        
        <?php if ($_SESSION['identity']->rol === 'admin'): ?>
            <div class="alert alert-info">
                <p>Eres administrador. Puedes gestionar:</p>
                <a href="<?= $base_url ?>/admin/categorias" class="btn">Categorías</a>
                <a href="<?= $base_url ?>/admin/productos" class="btn">Productos</a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p>Por favor, inicia sesión o regístrate para empezar a comprar.</p>
        <a href="<?= $base_url ?>/login" class="btn">Iniciar Sesión</a>
        <a href="<?= $base_url ?>/registro" class="btn">Registrarse</a>
    <?php endif; ?>

    <div class="actions" style="margin-top: 2rem;">
        <a href="<?= $base_url ?>/productos" class="btn">Ver Productos</a>
        <a href="<?= $base_url ?>/carrito" class="btn">Ir al Carrito</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>