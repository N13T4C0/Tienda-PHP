<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>Bienvenido a Tienda Online</h1>
    
    <?php if (isset($_SESSION['identity'])): ?>
        <p>Hola, <?= htmlspecialchars($_SESSION['identity']->nombre) ?> 
           <?= htmlspecialchars($_SESSION['identity']->apellidos) ?></p>
    <?php else: ?>
        <p>Por favor, inicia sesión o regístrate para empezar a comprar.</p>
    <?php endif; ?>

    <div class="actions">
        <a href="/productos" class="btn">Ver Productos</a>
        <a href="/carrito" class="btn">Ir al Carrito</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>