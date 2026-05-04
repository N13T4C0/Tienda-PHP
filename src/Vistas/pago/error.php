<?php require APP . '/Vistas/comunes/cabecera.php'; ?>

<div class="pago-resultado pago-resultado--error">
    <div class="pago-icono pago-icono--error">✕</div>
    <h2>Error en el pago</h2>
    <p>Algo ha ido mal al procesar el pago con PayPal.<br>Por favor, inténtalo de nuevo.</p>
    <div class="pago-resultado__acciones">
        <a class="boton boton-secundario" href="<?= URL_BASE ?>/cesta">Volver a la cesta</a>
    </div>
</div>