
<h1>Gestion de pedidos</h1>

<table class="tabla">
    <thead>
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Email</th>
            <th>Direccion</th>
            <th class="txt-derecha">Total</th>
            <th class="txt-centro">Detalle</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($pedidos)): ?>
            <tr>
                <td colspan="7" class="txt-centro">No hay pedidos todavia.</td>
            </tr>
        <?php endif; ?>
        <?php foreach ($pedidos as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['fecha_pedido']) ?></td>
                <td><?= htmlspecialchars($p['usuario_nombre']) ?></td>
                <td><?= htmlspecialchars($p['usuario_email']) ?></td>
                <td>
                    <?= htmlspecialchars($p['direccion']) ?>,
                    <?= htmlspecialchars($p['localidad']) ?>
                    (<?= htmlspecialchars($p['provincia']) ?>)
                </td>
                <td class="txt-derecha">
                    <?= number_format((float)$p['importe_total'], 2, ',', '.') ?> &euro;
                </td>
                <td class="txt-centro">
                    <a class="boton boton-pequeno"
                       href="<?= URL_BASE ?>/admin/detallePedido/<?= $p['id'] ?>">Ver lineas</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p class="volver"><a href="<?= URL_BASE ?>/admin">&larr; Volver al panel</a></p>
