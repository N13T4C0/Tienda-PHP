<?php
/**
 * Helper para enviar emails con la funcion nativa mail() de PHP.
 *
 * NOTA IMPORTANTE para 2DAW / XAMPP:
 *   - En XAMPP por defecto la funcion mail() NO funciona si no se configura
 *     un servidor SMTP en php.ini / sendmail.ini.
 *   - Para entornos de pruebas se puede:
 *       a) Configurar sendmail con una cuenta de Gmail (con clave de app),
 *       b) Usar el "modo simulacion" de esta clase, que en lugar de enviar
 *          escribe el correo en /storage/mails/*.html.
 *
 * Se ha intentado mantener el codigo simple y comprensible para 2DAW.
 */
class EnvioMail
{
    /** Si esta en true, los emails NO se envian, se guardan en disco */
    private const MODO_SIMULACION = true;

    /** Direccion remitente */
    private const REMITENTE       = 'no-responder@tiendaphp.local';
    private const NOMBRE_REMITE   = 'Tienda PHP';

    /**
     * Envia el email de confirmacion de registro.
     *
     * @param string $email     destinatario
     * @param string $nombre    nombre del usuario
     * @param string $token     token de activacion
     * @return bool
     */
    public static function confirmacionRegistro(string $email, string $nombre, string $token): bool
    {
        $enlace  = 'http://localhost' . URL_BASE . '/auth/confirmar/' . $token;
        // Nota: para que se abra desde el email se usa la URL absoluta
        // ajustada al servidor local de XAMPP (puerto 80).

        $asunto  = 'Confirma tu registro en Tienda PHP';
        $cuerpo  = "<h2>Hola {$nombre}!</h2>";
        $cuerpo .= "<p>Gracias por registrarte en nuestra tienda.</p>";
        $cuerpo .= "<p>Para activar tu cuenta haz clic en el siguiente enlace:</p>";
        $cuerpo .= "<p><a href='{$enlace}'>Activar mi cuenta</a></p>";
        $cuerpo .= "<p>Si tu no te has registrado, ignora este mensaje.</p>";

        return self::despachar($email, $asunto, $cuerpo);
    }

    /** Envia (o simula) el email */
    private static function despachar(string $para, string $asunto, string $cuerpoHtml): bool
    {
        // Modo simulacion: guardamos en disco
        if (self::MODO_SIMULACION) {
            $carpeta = RAIZ . '/storage/mails';
            if (!is_dir($carpeta)) {
                @mkdir($carpeta, 0777, true);
            }
            $nombreArchivo = $carpeta . '/' . date('Ymd_His') . '_' . uniqid() . '.html';
            $contenido     = "<!-- Para: {$para} | Asunto: {$asunto} -->\n" . $cuerpoHtml;
            return (bool) file_put_contents($nombreArchivo, $contenido);
        }

        // Modo real: usar mail()
        $cabeceras  = "MIME-Version: 1.0\r\n";
        $cabeceras .= "Content-type: text/html; charset=utf-8\r\n";
        $cabeceras .= 'From: ' . self::NOMBRE_REMITE . ' <' . self::REMITENTE . ">\r\n";
        return mail($para, $asunto, $cuerpoHtml, $cabeceras);
    }
}
