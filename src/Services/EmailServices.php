<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->configurar();
    }

    private function configurar() {
        $this->mail->isSMTP();
        $this->mail->Host = $_ENV['SMTP_HOST'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $_ENV['SMTP_USER'];
        $this->mail->Password = $_ENV['SMTP_PASS'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = $_ENV['SMTP_PORT'];
        $this->mail->CharSet = 'UTF-8';
        $this->mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']);
    }

    public function enviarConfirmacionRegistro($usuario, $token) {
        try {
            $urlConfirmacion = $_ENV['APP_URL'] . '/confirmar/' . $token;
            
            $this->mail->addAddress($usuario->email, $usuario->nombre);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Confirma tu registro en Tienda Online';
            
            $this->mail->Body = "
                <h2>¡Bienvenido {$usuario->nombre}!</h2>
                <p>Gracias por registrarte en nuestra tienda.</p>
                <p>Para confirmar tu cuenta, haz clic en el siguiente enlace:</p>
                <p><a href='{$urlConfirmacion}'>Confirmar mi cuenta</a></p>
                <p>Este enlace expirará en 24 horas.</p>
                <p>Si no te has registrado, ignora este mensaje.</p>
            ";
            
            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Error al enviar email: " . $this->mail->ErrorInfo);
            return false;
        }
    }

    public function enviarConfirmacionPedido($pedido, $usuario, $lineas) {
        try {
            $this->mail->addAddress($usuario->email, $usuario->nombre);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Confirmación de Pedido #' . $pedido->id;
            
            $htmlLineas = '';
            foreach ($lineas as $linea) {
                $htmlLineas .= "
                    <tr>
                        <td>{$linea['nombre']}</td>
                        <td>{$linea['unidades']}</td>
                        <td>" . number_format($linea['precio_unitario'], 2) . " €</td>
                        <td>" . number_format($linea['subtotal'], 2) . " €</td>
                    </tr>
                ";
            }
            
            $this->mail->Body = "
                <h2>¡Pedido confirmado!</h2>
                <p>Hola {$usuario->nombre},</p>
                <p>Tu pedido <strong>#{$pedido->id}</strong> ha sido procesado correctamente.</p>
                
                <h3>Detalles del pedido:</h3>
                <table border='1' cellpadding='10'>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$htmlLineas}
                    </tbody>
                </table>
                
                <p><strong>Total: " . number_format($pedido->coste_total, 2) . " €</strong></p>
                
                <h3>Dirección de envío:</h3>
                <p>
                    {$pedido->direccion}<br>
                    {$pedido->localidad}, {$pedido->provincia}
                </p>
                
                <p>Recibirás un email cuando tu pedido sea enviado.</p>
            ";
            
            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Error al enviar email: " . $this->mail->ErrorInfo);
            return false;
        }
    }
}