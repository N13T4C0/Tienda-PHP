<div class="formulario ancho">
    <h1 class="formulario__titulo">Crear una cuenta</h1>

    <form method="POST" action="<?= URL_BASE ?>/auth/procesarRegistro">

        <div class="campo">
            <label for="nombre">Nombre *</label>
            <input type="text" name="nombre" id="nombre" required
                   value="<?= htmlspecialchars($datosPrevios['nombre'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="apellidos">Apellidos</label>
            <input type="text" name="apellidos" id="apellidos"
                   value="<?= htmlspecialchars($datosPrevios['apellidos'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" required
                   value="<?= htmlspecialchars($datosPrevios['email'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="clave">Contrasena * <small>(minimo 6 caracteres)</small></label>
            <input type="password" name="clave" id="clave" required minlength="6">
        </div>

        <div class="campo">
            <label for="clave2">Repetir contrasena *</label>
            <input type="password" name="clave2" id="clave2" required minlength="6">
        </div>

        <button type="submit" class="boton boton--full">Registrarme</button>
    </form>

    <p class="formulario__pie">
        ¿Ya tienes cuenta? <a href="<?= URL_BASE ?>/auth/login">Inicia sesion</a>
    </p>
</div>