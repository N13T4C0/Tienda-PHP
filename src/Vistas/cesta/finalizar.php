<h1>Finalizar compra</h1>

<div class="layout-tienda" style="grid-template-columns: 1fr 320px;">

    <div class="formulario ancho" style="margin:0;max-width:none;">
        <h2>Direccion de envio</h2>

        <form method="POST" action="<?= URL_BASE ?>/cesta/confirmar">
            <div class="campo">
                <label for="direccion">Direccion *</label>
                <input type="text" name="direccion" id="direccion" required>
            </div>
            <div class="campo">
                <label for="localidad">Localidad *</label>
                <input type="text" name="localidad" id="localidad" required>
            </div>
            <div class="campo">
                <label for="provincia">Provincia</label>
                <input type="text" name="provincia" id="provincia">
            </div>

            <button class="boton" type="submit" style="width:100%;">Confirmar pedido</button>
        </form>
    </div>

    <aside class="resumen-cesta" style="margin:0;">
        <h3>Resumen</h3>
        <table style="width:100%;margin-top:.5rem;">
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['producto']['nombre']) ?>
                        <small>x<?= $item['cantidad'] ?></small></td>
                    <td class="txt-derecha"><?= number_format($item['subtotal'], 2) ?> &euro;</td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p class="total">Total: <?= number_format($total, 2) ?> &euro;</p>
    </aside>
</div>
