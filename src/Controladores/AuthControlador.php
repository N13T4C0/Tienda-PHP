<?php
namespace Controladores;

// cambio pedido maestra
use Core\BaseControlador;
use Lib\Sesion;
use Lib\Cesta;
use Lib\EnvioMail;
use Lib\GoogleOAuth;
use Servicios\UsuarioServicio;
use Requests\RegistroRequest;
use Requests\LoginRequest;

class AuthControlador extends BaseControlador
{
    // Muestra el formulario de registro
    public function registro(): void
    {
        $datosPrevios = $_SESSION['datos_form'] ?? [];
        unset($_SESSION['datos_form']);

        $this->view('auth/registro', [
            'datosPrevios' => $datosPrevios,
        ]);
    }

    // Procesa el formulario de registro
    public function procesarRegistro(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sesion::redirigir('auth/registro');
        }

        $resultado    = RegistroRequest::validar($_POST);
        $errores      = $resultado['errores'];
        $datosLimpios = $resultado['datos'];

        $nombre    = $datosLimpios['nombre'];
        $apellidos = $datosLimpios['apellidos'];
        $email     = $datosLimpios['email'];
        $clave     = $datosLimpios['clave'];

        // Guardamos los datos para repintar el formulario si hay errores
        $_SESSION['datos_form'] = compact('nombre', 'apellidos', 'email');

        $servicio = new UsuarioServicio();

        if (!$errores && $servicio->emailExiste($email)) {
            $errores[] = 'Ya existe una cuenta con ese email';
        }

        if ($errores) {
            Sesion::mensaje('error', implode('<br>', $errores));
            Sesion::redirigir('auth/registro');
        }

        $token = $servicio->registrar([
            'nombre'    => $nombre,
            'apellidos' => $apellidos,
            'email'     => $email,
            'clave'     => $clave,
        ]);

        unset($_SESSION['datos_form']);

        EnvioMail::confirmacionRegistro($email, $nombre, $token);

        Sesion::mensaje('ok', 'Registro completado, revisa tu email');
        Sesion::redirigir('auth/login');
    }

    // Activa la cuenta del usuario mediante el enlace del email
    public function confirmar($token = null): void
    {
        if (!$token) {
            Sesion::mensaje('error', 'Token no valido');
            Sesion::redirigir('auth/login');
        }

        $servicio = new UsuarioServicio();
        $usuario  = $servicio->activarCuenta($token);

        if (!$usuario) {
            Sesion::mensaje('error', 'El enlace no es valido, ya se uso o ha expirado (1 minuto)');
            Sesion::redirigir('auth/login');
        }

        Sesion::mensaje('ok', 'Cuenta activada. Ya puedes iniciar sesion.');
        Sesion::redirigir('auth/login');
    }

    // Muestra el formulario de login
    public function login(): void
    {
        $this->view('auth/login');
    }

    // Procesa el formulario de login
    public function procesarLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sesion::redirigir('auth/login');
        }

        $resultado    = LoginRequest::validar($_POST);
        $errores      = $resultado['errores'];
        $datosLimpios = $resultado['datos'];

        $email = $datosLimpios['email'];
        $clave = $datosLimpios['clave'];

        if ($errores) {
            Sesion::mensaje('error', implode('<br>', $errores));
            Sesion::redirigir('auth/login');
        }

        $servicio = new UsuarioServicio();
        $usuario  = $servicio->verificarCredenciales($email, $clave);

        if (!$usuario) {
            Sesion::mensaje('error', 'Credenciales incorrectas');
            Sesion::redirigir('auth/login');
        }

        if (!$usuario['activado']) {
            Sesion::mensaje('error', 'Tu cuenta aun no esta activada');
            Sesion::redirigir('auth/login');
        }

        Sesion::iniciar($usuario);
        Cesta::migrarAlLogin((int) $usuario['id']);
        Sesion::mensaje('ok', 'Bienvenido, ' . $usuario['nombre']);
        Sesion::redirigir('');
    }

    // Cierra la sesion del usuario
    public function logout(): void
    {
        Sesion::cerrar();
        Sesion::mensaje('info', 'Sesion cerrada correctamente');
        Sesion::redirigir('');
    }

    // Redirige al usuario a la pantalla de login de Google
    public function loginGoogle(): void
    {
        $google = $this->crearClienteGoogle();
        $state  = bin2hex(random_bytes(16));

        $_SESSION['oauth_state'] = $state;

        header('Location: ' . $google->getAuthUrl($state));
        exit;
    }

    // Google redirige aqui despues de que el usuario acepte los permisos
    public function googleCallback(): void
    {
        if (!isset($_GET['code'], $_GET['state'])) {
            Sesion::mensaje('error', 'Respuesta de Google incorrecta');
            Sesion::redirigir('auth/login');
        }

        if (!hash_equals($_SESSION['oauth_state'] ?? '', $_GET['state'])) {
            unset($_SESSION['oauth_state']);
            Sesion::mensaje('error', 'Error de seguridad. Intentalo de nuevo');
            Sesion::redirigir('auth/login');
        }
        unset($_SESSION['oauth_state']);

        $google    = $this->crearClienteGoogle();
        $tokenData = $google->intercambiarCode($_GET['code']);

        if (isset($tokenData['error']) || empty($tokenData['access_token'])) {
            Sesion::mensaje('error', 'Google rechazo la solicitud');
            Sesion::redirigir('auth/login');
        }

        $info = $google->getUserInfo($tokenData['access_token']);

        if (empty($info['email']) || !($info['email_verified'] ?? false)) {
            Sesion::mensaje('error', 'No se pudo verificar tu cuenta de Google');
            Sesion::redirigir('auth/login');
        }

        $servicio = new UsuarioServicio();
        $usuario  = $servicio->procesarLoginGoogle($info);

        Sesion::iniciar($usuario);
        Cesta::migrarAlLogin((int) $usuario['id']);
        Sesion::mensaje('ok', 'Bienvenido, ' . $usuario['nombre']);
        Sesion::redirigir('');
    }

    // Crea el cliente de Google con las credenciales del archivo de configuracion
    private function crearClienteGoogle(): GoogleOAuth
    {
        $config = require APP . '/../config/google.php';

        return new GoogleOAuth(
            $config['client_id'],
            $config['client_secret'],
            $config['redirect_uri']
        );
    }
}
