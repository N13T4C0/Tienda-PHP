<?php

use Lib\Cesta;
use Lib\Sesion;

// Inicializamos la cesta para que se pueda mostrar el contador en la cabecera
Cesta::preparar();
$_unidadesCesta = Cesta::totalUnidades();
$_usuario       = Sesion::usuario();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>netStore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/css/estilo.css">
</head>

<body>

    <header class="cabecera">
        <div class="cabecera-contenido">
            <a class="logo" href="<?= URL_BASE ?>/">net<span>Store</span></a>

            <ul class="menu">
                <li><a href="<?= URL_BASE ?>/">Inicio</a></li>
                <li><a href="<?= URL_BASE ?>/producto">Catálogo</a></li>
                <li>
                    <a href="<?= URL_BASE ?>/cesta">
                        Cesta <span class="contador-cesta"><?= $_unidadesCesta ?></span>
                    </a>
                </li>
                <?php if ($_usuario): ?>
                    <?php
                    // Accedemos como array u objeto según cómo guardes la sesión, 
                    // aquí mantenemos array por compatibilidad con Lib\Sesion estándar
                    if ($_usuario['rol'] === 'admin'): ?>
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
            $_tipo = $_flash['tipo'];
            $_clase = 'alerta-info';
            if ($_tipo === 'error') $_clase = 'alerta-error';
            if ($_tipo === 'ok' || $_tipo === 'exito') $_clase = 'alerta-ok';
        ?>
            <div class="alerta <?= $_clase ?>"><?= htmlspecialchars($_flash['texto']) ?></div>
        <?php endif; ?>