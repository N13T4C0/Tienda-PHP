
<div class="pago-resultado pago-resultado--ok">
    <div class="pago-icono pago-icono--ok">✓</div>
    <h2>¡Pago completado!</h2>
    <p>Tu pedido ha sido registrado correctamente.<br>Recibirás un correo con los detalles.</p>
    <div class="pago-resultado__acciones">
        <?php if (!empty($idPedido)): ?>
            <a class="boton" href="<?= URL_BASE ?>/pago/factura">
                Descargar factura PDF
            </a>
        <?php endif; ?>
        <a class="boton boton-secundario" href="<?= URL_BASE ?>/producto">Seguir comprando</a>
    </div>
</div>
