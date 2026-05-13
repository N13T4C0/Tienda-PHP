
<h1>Detalle del pedido #<?= $pedido['id'] ?></h1>

<!-- Datos generales del pedido -->
<div class="formulario" style="margin-bottom:2rem;">
    <p>
        <strong>Cliente:</strong>
        <?= htmlspecialchars($pedido['usuario_nombre']) ?>
        &lt;<?= htmlspecialchars($pedido['usuario_email']) ?>&gt;
    </p>
    <p>
        <strong>Fecha:</strong>
        <?= htmlspecialchars($pedido['fecha_pedido']) ?>
    </p>
    <p>
        <strong>Direccion de envio:</strong>
        <?= htmlspecialchars($pedido['direccion']) ?>,
        <?= htmlspecialchars($pedido['localidad']) ?>
        (<?= htmlspecialchars($pedido['provincia']) ?>)
    </p>
    <p>
        <strong>Total del pedido:</strong>
        <?= number_format((float)$pedido['importe_total'], 2, ',', '.') ?> &euro;
    </p>
</div>

<!-- Lineas del pedido -->
<h2>Productos del pedido</h2>

<table class="tabla">
    <thead>
        <tr>
            <th>Producto</th>
            <th class="txt-centro">Unidades</th>
            <th class="txt-derecha">Precio unidad</th>
            <th class="txt-derecha">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($lineas)): ?>
            <tr>
                <td colspan="4" class="txt-centro">Este pedido no tiene lineas.</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($lineas as $l): ?>
            <tr>
                <td><?= htmlspecialchars($l['nombre_producto']) ?></td>
                <td class="txt-centro"><?= (int)$l['unidades'] ?></td>
                <td class="txt-derecha">
                    <?= number_format((float)$l['precio_unidad'], 2, ',', '.') ?> &euro;
                </td>
                <td class="txt-derecha">
                    <?= number_format((float)$l['subtotal'], 2, ',', '.') ?> &euro;
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <?php if (!empty($lineas)): ?>
        <tfoot>
            <tr>
                <td colspan="3" class="txt-derecha"><strong>Total</strong></td>
                <td class="txt-derecha">
                    <strong>
                        <?= number_format((float)$pedido['importe_total'], 2, ',', '.') ?> &euro;
                    </strong>
                </td>
            </tr>
        </tfoot>
    <?php endif; ?>
</table>

<p class="volver"><a href="<?= URL_BASE ?>/admin/pedidos">&larr; Volver a pedidos</a></p>
