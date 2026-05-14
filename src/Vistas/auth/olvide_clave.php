<div class="formulario">
    <h1 class="formulario__titulo">¿Olvidaste tu contraseña?</h1>
    <p style="text-align:center;color:#666;margin-bottom:1.5rem;">
        Introduce tu email y te enviaremos un enlace para restablecerla.
    </p>
    <form method="POST" action="<?= URL_BASE ?>/auth/procesarOlvideClave">
        <div class="campo">
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" required>
        </div>
        <button class="boton boton--full" type="submit">Enviar enlace</button>
    </form>
    <p class="formulario__pie">
        <a href="<?= URL_BASE ?>/auth/login">&larr; Volver al login</a>
    </p>
</div>