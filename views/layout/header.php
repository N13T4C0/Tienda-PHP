<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link rel="stylesheet" href="/public/css/estilos.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="/">Tienda Online</a>
            </div>
            <ul class="menu">
                <li><a href="/">Inicio</a></li>
                <li><a href="/productos">Productos</a></li>
            <li>
                <a href="/carrito">
                    🛒 Carrito 
                    <?php
                    if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
                        $total = array_sum($_SESSION['carrito']);
                        echo "({$total})";
                    } else {
                        echo "(0)";
                    }
                    ?>
                </a>
            </li>                
                <?php if (isset($_SESSION['identity'])): ?>
                    <?php if ($_SESSION['identity']->rol === 'admin'): ?>
                        <li><a href="/admin/categorias">Admin Categorías</a></li>
                        <li><a href="/admin/productos">Admin Productos</a></li>
                    <?php endif; ?>
                    <li><a href="/pedidos/mis-pedidos">Mis Pedidos</a></li>
                    <li>Hola, <?= htmlspecialchars($_SESSION['identity']->nombre) ?></li>
                    <li><a href="/logout">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="/login">Login</a></li>
                    <li><a href="/registro">Registro</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <?php require_once __DIR__ . '/mensajes.php'; ?>