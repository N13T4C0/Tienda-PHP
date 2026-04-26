<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <?php
    $base_url = dirname($_SERVER['SCRIPT_NAME']);
    ?>
    <link rel="stylesheet" href="<?= $base_url ?>/css/estilos.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="<?= $base_url ?>/">Tienda Online</a>
            </div>
            <ul class="menu">
                <li><a href="<?= $base_url ?>/">Inicio</a></li>
                <li><a href="<?= $base_url ?>/productos">Productos</a></li>
            <li>
                <a href="<?= $base_url ?>/carrito">
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
                    <?php 
                    // Asegurarnos de que identity es un objeto y no un array
                    $identity = $_SESSION['identity'];
                    if (is_array($identity)) {
                        // Convertir array a objeto si es necesario
                        $identity = (object)$identity;
                    }
                    ?>
                    <?php if (isset($identity->rol) && $identity->rol === 'admin'): ?>
                        <li><a href="<?= $base_url ?>/admin/categorias">Admin Categorías</a></li>
                        <li><a href="<?= $base_url ?>/admin/productos">Admin Productos</a></li>
                    <?php endif; ?>
                    <li><a href="<?= $base_url ?>/pedidos/mis-pedidos">Mis Pedidos</a></li>
                    <li>Hola, <?= htmlspecialchars(isset($identity->nombre) ? $identity->nombre : 'Usuario') ?></li>
                    <li><a href="<?= $base_url ?>/logout">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="<?= $base_url ?>/login">Login</a></li>
                    <li><a href="<?= $base_url ?>/registro">Registro</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <?php require_once __DIR__ . '/mensajes.php'; ?>