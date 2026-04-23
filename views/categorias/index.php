<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>Gestión de Categorías</h1>
    
    <a href="/admin/categorias/crear" class="btn">Nueva Categoría</a>

    <?php if (empty($categorias)): ?>
        <p>No hay categorías registradas</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= $categoria->id ?></td>
                        <td><?= htmlspecialchars($categoria->nombre) ?></td>
                        <td><?= htmlspecialchars($categoria->descripcion) ?></td>
                        <td><?= date('d/m/Y', strtotime($categoria->created_at)) ?></td>
                        <td>
                            <a href="/admin/categorias/editar/<?= $categoria->id ?>" class="btn-small">Editar</a>
                            <form action="/admin/categorias/eliminar/<?= $categoria->id ?>" method="POST" style="display: inline;">
                                <button type="submit" class="btn-danger" 
                                        onclick="return confirm('¿Eliminar esta categoría?')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>