<div class="formulario">
    <h1 style="text-align:center;margin-bottom:1.5rem;">Iniciar sesion</h1>

    <form method="POST" action="<?= URL_BASE ?>/auth/procesarLogin">

        <div class="campo">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required autofocus>
        </div>

        <div class="campo">
            <label for="clave">Contrasena</label>
            <input type="password" name="clave" id="clave" required>
        </div>

        <button type="submit" class="boton" style="width:100%;">Entrar</button>
    </form>

    <p style="margin-top:1rem;text-align:center;">
        ¿No tienes cuenta? <a href="<?= URL_BASE ?>/auth/registro">Registrate aqui</a>
    </p>
</div>
