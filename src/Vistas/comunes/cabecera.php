<?php
// Inicializamos la cesta para que se pueda mostrar el contador en la cabecera
Cesta::preparar();
$_unidadesCesta = Cesta::totalUnidades();
$_usuario       = Sesion::usuario();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda PHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/css/estilo.css">
</head>
<body>

<header class="cabecera">
    <div class="cabecera-contenido">
        <a class="logo" href="<?= URL_BASE ?>/">Tienda<span>PHP</span></a>

        <form action="<?= URL_BASE ?>/producto/buscar" method="GET" style="flex:1;max-width:300px;">
            <input type="text" name="q" placeholder="Buscar productos..."
                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                   style="width:100%;padding:.5rem;border-radius:6px;border:0;">
        </form>

        <ul class="menu">
            <li><a href="<?= URL_BASE ?>/">Inicio</a></li>
            <li><a href="<?= URL_BASE ?>/producto">Catalogo</a></li>
            <li>
                <a href="<?= URL_BASE ?>/cesta">
                    Cesta <span class="contador-cesta"><?= $_unidadesCesta ?></span>
                </a>
            </li>
            <?php if ($_usuario): ?>
                <?php if ($_usuario['rol'] === 'admin'): ?>
                    <li><a href="<?= URL_BASE ?>/admin">Panel</a></li>
                <?php endif; ?>
                <li><a href="<?= URL_BASE ?>/auth/logout">Salir (<?= htmlspecialchars($_usuario['nombre']) ?>)</a></li>
            <?php else: ?>
                <li><a href="<?= URL_BASE ?>/auth/login">Entrar</a></li>
                <li><a href="<?= URL_BASE ?>/auth/registro">Registro</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>

<main class="contenedor">

<?php
$_flash = Sesion::consumirMensaje();
if ($_flash):
    $_clase = 'alerta-' . ($_flash['tipo'] === 'error' ? 'error' : ($_flash['tipo'] === 'ok' ? 'ok' : 'info'));
?>
    <div class="alerta <?= $_clase ?>"><?= $_flash['texto'] ?></div>
<?php endif; ?>
