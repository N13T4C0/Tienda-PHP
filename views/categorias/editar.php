<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>Editar Categoría</h1>
    
    <form action="/admin/categorias/actualizar/<?= $categoria->id ?>" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" 
                   value="<?= $_SESSION['old']['nombre'] ?? $categoria->nombre ?>" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4"><?= $_SESSION['old']['descripcion'] ?? $categoria->descripcion ?></textarea>
        </div>

        <button type="submit">Actualizar Categoría</button>
        <a href="/admin/categorias" class="btn">Cancelar</a>
    </form>
</div>

<?php 
unset($_SESSION['old']); 
require_once __DIR__ . '/../layout/footer.php'; 
?>