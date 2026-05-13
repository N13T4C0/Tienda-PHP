<h1>Tu cesta</h1>

<?php if (empty($items)): ?>
    <div class="bloque-vacio">
        <p>Tu cesta está vacía.</p>
        <p class="bloque-vacio__accion">
            <a class="boton" href="<?= URL_BASE ?>/producto">Ver catálogo</a>
        </p>
    </div>
<?php else: ?>

    <table class="tabla">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="txt-centro">Precio</th>
                <th class="txt-centro">Cantidad</th>
                <th class="txt-derecha">Subtotal</th>
                <th class="txt-centro">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <?php $p = $item['producto']; // Para abreviar 
                ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($p->nombre) ?></strong><br>
                        <small class="tabla__categoria">
                            <?= htmlspecialchars($p->categoria_nombre ?? 'Sin categoría') ?>
                        </small>
                    </td>
                    <td class="txt-centro"><?= number_format($p->precio, 2) ?> &euro;</td>
                    <td class="txt-centro">
                        <form method="POST" action="<?= URL_BASE ?>/cesta/actualizar" class="form-cantidad">
                            <input type="hidden" name="id_producto" value="<?= $p->id ?>">
                            <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>"
                                min="1" max="<?= $p->stock ?>"
                                class="input-cantidad">
                            <button type="submit" class="boton boton-pequeno">OK</button>
                        </form>
                    </td>
                    <td class="txt-derecha"><?= number_format($item['subtotal'], 2) ?> &euro;</td>
                    <td class="txt-centro">
                        <a href="<?= URL_BASE ?>/cesta/quitar/<?= $p->id ?>"
                            class="boton boton-pequeno boton-borrar">Quitar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="resumen-cesta">
        <div class="acciones">
            <a class="boton boton-secundario" href="<?= URL_BASE ?>/producto">Seguir comprando</a>
            <a class="boton boton-borrar" href="<?= URL_BASE ?>/cesta/vaciar">Vaciar cesta</a>
        </div>
        <p class="total">Total: <?= number_format($total, 2) ?> &euro;</p>
        <p class="txt-derecha">
            <a class="boton" href="<?= URL_BASE ?>/cesta/finalizar">Finalizar compra</a>
        </p>
    </div>

<?php endif; ?>