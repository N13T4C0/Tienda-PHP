<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1>Iniciar Sesión</h1>
    
    <form action="<?= $base_url ?>/login" method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Iniciar Sesión</button>
        <a href="<?= $base_url ?>/registro" class="btn">Crear cuenta</a>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>