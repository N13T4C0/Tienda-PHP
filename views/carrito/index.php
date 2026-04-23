<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>Carrito de Compra</h1>

    <?php if (empty($carrito)): ?>
        <div class="alert alert-info">
            <p>Tu carrito está vacío</p>
            <a href="/productos" class="btn">Ver Productos</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrito as $item): ?>
                    <tr>
                        <td>
                            <?php if ($item['producto']->imagen): ?>
                                <img src="/public/uploads/productos/<?= htmlspecialchars($item['producto']->imagen) ?>" 
                                     alt="<?= htmlspecialchars($item['producto']->nombre) ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            <?php endif; ?>
                            <?= htmlspecialchars($item['producto']->nombre) ?>
                        </td>
                        <td><?= number_format($item['producto']->precio, 2) ?> €</td>
                        <td>
                            <form action="/carrito/actualizar" method="POST" style="display: inline;">
                                <input type="hidden" name="producto_id" value="<?= $item['producto']->id ?>">
                                <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" 
                                       min="1" max="<?= $item['producto']->stock ?>" style="width: 60px;">
                                <button type="submit" class="btn-small">Actualizar</button>
                            </form>
                        </td>
                        <td><?= number_format($item['subtotal'], 2) ?> €</td>
                        <td>
                            <form action="/carrito/eliminar" method="POST" style="display: inline;">
                                <input type="hidden" name="producto_id" value="<?= $item['producto']->id ?>">
                                <button type="submit" class="btn-danger" 
                                        onclick="return confirm('¿Eliminar este producto?')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                    <td colspan="2"><strong><?= number_format($total, 2) ?> €</strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="carrito-acciones" style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: space-between;">
            <div>
                <form action="/carrito/vaciar" method="POST" style="display: inline;">
                    <button type="submit" class="btn-danger" 
                            onclick="return confirm('¿Vaciar todo el carrito?')">
                        Vaciar Carrito
                    </button>
                </form>
                <a href="/productos" class="btn">Seguir Comprando</a>
            </div>
            <div>
                <?php if (isset($_SESSION['identity'])): ?>
                    <a href="/pedidos/confirmar" class="btn btn-success">Finalizar Pedido</a>
                <?php else: ?>
                    <a href="/login" class="btn btn-success">Iniciar Sesión para Comprar</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>