<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Helper estatico para enviar emails usando PHPMailer + SMTP.
 *
 * Las credenciales se leen del archivo .env (nunca hardcodeadas):
 *   SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_FROM, SMTP_FROM_NAME
 *
 * En desarrollo: usa Mailtrap (sandbox.smtp.mailtrap.io, puerto 2525).
 * En produccion: sustituye las credenciales por las de Gmail u otro proveedor.
 */
class EnvioMail
{
    /**
     * Email de confirmacion de registro.
     * Se llama al registrar un usuario nuevo para que active su cuenta.
     */
    public static function confirmacionRegistro(string $email, string $nombre, string $token): bool
    {
        $enlace = 'http://localhost' . URL_BASE . '/auth/confirmar/' . $token;
        $asunto = 'Confirma tu registro en Tienda PHP';

        $cuerpo = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;padding:24px;
                    border:1px solid #e0e0e0;border-radius:8px;'>
            <h2 style='color:#1e3a5f;'>Hola, {$nombre}!</h2>
            <p>Gracias por registrarte en <strong>Tienda PHP</strong>.</p>
            <p>Para activar tu cuenta haz clic en el boton:</p>
            <p style='text-align:center;margin:32px 0;'>
                <a href='{$enlace}'
                   style='background:#2563eb;color:#fff;padding:12px 28px;
                          border-radius:6px;text-decoration:none;font-weight:bold;'>
                    Activar mi cuenta
                </a>
            </p>
            <p style='color:#888;font-size:13px;'>Si no te has registrado tu, ignora este mensaje.</p>
        </div>";

        return self::enviar($email, $asunto, $cuerpo);
    }

    /**
     * Email de confirmacion de pedido.
     * Se llama tras crear el pedido en la base de datos.
     *
     * @param string $email    Correo del cliente
     * @param string $nombre   Nombre del cliente
     * @param int    $idPedido ID del pedido recien creado
     * @param array  $items    Lineas del carrito: [['producto'=>[...], 'cantidad'=>N], ...]
     * @param float  $total    Importe total
     * @param array  $envio    ['direccion', 'localidad', 'provincia']
     */
    public static function confirmacionPedido(
        string $email,
        string $nombre,
        int    $idPedido,
        array  $items,
        float  $total,
        array  $envio
    ): bool {
        $asunto = "Confirmacion de tu pedido #{$idPedido} - Tienda PHP";
        $fecha  = date('d/m/Y H:i');

        // Construimos la tabla de productos del pedido
        $filas = '';
        foreach ($items as $item) {
            $p        = $item['producto'];
            $cantidad = (int) $item['cantidad'];
            $subtotal = number_format($p['precio'] * $cantidad, 2, ',', '.') . ' &euro;';
            $precio   = number_format($p['precio'], 2, ',', '.') . ' &euro;';
            $filas   .= "
            <tr>
                <td style='padding:8px;border-bottom:1px solid #eee;'>"
                    . htmlspecialchars($p['nombre']) . "
                </td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:center;'>{$cantidad}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>{$precio}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>{$subtotal}</td>
            </tr>";
        }

        $totalFmt  = number_format($total, 2, ',', '.') . ' &euro;';
        $direccion = htmlspecialchars(
            $envio['direccion'] . ', ' . $envio['localidad'] . ' (' . ($envio['provincia'] ?? '') . ')'
        );

        $cuerpo = "
        <div style='font-family:Arial,sans-serif;max-width:650px;margin:auto;padding:24px;
                    border:1px solid #e0e0e0;border-radius:8px;'>
            <h2 style='color:#1e3a5f;'>Gracias por tu compra, {$nombre}!</h2>
            <p>Hemos recibido tu pedido correctamente. Aqui tienes el resumen:</p>

            <table style='width:100%;border-collapse:collapse;margin-top:16px;font-size:14px;'>
                <thead>
                    <tr style='background:#f0f4f8;'>
                        <th style='padding:10px 8px;text-align:left;'>Producto</th>
                        <th style='padding:10px 8px;text-align:center;'>Uds.</th>
                        <th style='padding:10px 8px;text-align:right;'>Precio</th>
                        <th style='padding:10px 8px;text-align:right;'>Subtotal</th>
                    </tr>
                </thead>
                <tbody>{$filas}</tbody>
                <tfoot>
                    <tr>
                        <td colspan='3'
                            style='padding:12px 8px;text-align:right;font-weight:bold;'>TOTAL</td>
                        <td style='padding:12px 8px;text-align:right;font-weight:bold;
                                   color:#2563eb;font-size:16px;'>{$totalFmt}</td>
                    </tr>
                </tfoot>
            </table>

            <p style='margin-top:20px;'><strong>Numero de pedido:</strong> #{$idPedido}</p>
            <p><strong>Fecha:</strong> {$fecha}</p>
            <p><strong>Direccion de envio:</strong> {$direccion}</p>

            <p style='color:#888;font-size:13px;margin-top:24px;'>
                Gracias por comprar en <strong>Tienda PHP</strong>.
            </p>
        </div>";

        return self::enviar($email, $asunto, $cuerpo);
    }

    /**
     * Metodo privado que construye el PHPMailer y envia el correo via SMTP.
     * Lee las credenciales del archivo .env cargado en el bootstrap (init.php).
     */
    private static function enviar(string $para, string $asunto, string $cuerpoHtml): bool
    {
        $mail = new PHPMailer(true); // true = activa excepciones

        try {
            // --- Servidor SMTP ---
            $mail->isSMTP();
            $mail->Host       = Utilidades::obtener('SMTP_HOST', 'sandbox.smtp.mailtrap.io');
            $mail->SMTPAuth   = true;
            $mail->Username   = Utilidades::obtener('SMTP_USER', '');
            $mail->Password   = Utilidades::obtener('SMTP_PASS', '');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) Utilidades::obtener('SMTP_PORT', 2525);

            // --- Remitente y destinatario ---
            $mail->setFrom(
                Utilidades::obtener('SMTP_FROM',      'no-responder@tiendaphp.local'),
                Utilidades::obtener('SMTP_FROM_NAME', 'Tienda PHP')
            );
            $mail->addAddress($para);

            // --- Contenido ---
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $asunto;
            $mail->Body    = $cuerpoHtml;

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('EnvioMail ERROR: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
