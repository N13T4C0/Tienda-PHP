<h1>Finalizar compra</h1>

<div class="layout-tienda layout-tienda--checkout">

    <div class="formulario formulario--checkout">
        <h2>Dirección de envío</h2>

        <form method="POST" action="<?= URL_BASE ?>/cesta/confirmar">
            <div class="campo">
                <label for="direccion">Dirección *</label>
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

            <button class="boton boton--full" type="submit">Confirmar pedido</button>
        </form>
    </div>

    <aside class="resumen-cesta resumen-cesta--checkout">
        <h3>Resumen</h3>
        <table class="resumen-cesta__tabla">
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($item['producto']->nombre) ?>
                        <small>x<?= $item['cantidad'] ?></small>
                    </td>
                    <td class="txt-derecha"><?= number_format($item['subtotal'], 2) ?> &euro;</td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p class="total">Total: <?= number_format($total, 2) ?> &euro;</p>
    </aside>
</div>