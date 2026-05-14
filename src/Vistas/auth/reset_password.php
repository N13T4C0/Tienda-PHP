<div class="formulario">
    <h1 class="formulario__titulo">Nueva contraseña</h1>
    <form method="POST" action="<?= URL_BASE ?>/auth/procesarresetpassword">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="campo">
            <label for="clave">Nueva contraseña *</label>
            <input type="password" name="clave" id="clave" required minlength="6">
        </div>
        <div class="campo">
            <label for="clave2">Repetir contraseña *</label>
            <input type="password" name="clave2" id="clave2" required minlength="6">
        </div>
        <button class="boton boton--full" type="submit">Restablecer contraseña</button>
    </form>
</div>