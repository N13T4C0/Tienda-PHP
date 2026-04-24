<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>Registro de Usuario</h1>
    
    <form action="<?= $base_url ?>/registro" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" 
                   value="<?= $_SESSION['old']['nombre'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" 
                   value="<?= $_SESSION['old']['apellidos'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" 
                   value="<?= $_SESSION['old']['email'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Registrarse</button>
        <a href="<?= $base_url ?>/login" class="btn">Ya tengo cuenta</a>
    </form>
</div>

<?php 
unset($_SESSION['old']); 
require_once __DIR__ . '/../layout/footer.php'; 
?>