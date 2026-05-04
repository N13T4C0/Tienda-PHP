<?php
namespace Controladores;

use Lib\Sesion;
use Lib\Cesta;
use Lib\EnvioMail;
use Lib\GoogleOAuth;
use Servicios\UsuarioServicio;
use Requests\RegistroRequest;
use Requests\LoginRequest;

class AuthControlador
{
    // Muestra el formulario de registro
    public function registro(): void
    {
        $datosPrevios = $_SESSION['datos_form'] ?? [];
        unset($_SESSION['datos_form']);

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/auth/registro.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    // Procesa el formulario de registro
    public function procesarRegistro(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sesion::redirigir('auth/registro');
        }

        $nombre    = trim($_POST['nombre']    ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $email     = trim($_POST['email']     ?? '');
        $clave     = $_POST['clave']          ?? '';
        $clave2    = $_POST['clave2']         ?? '';

        // Guardamos los datos para repintar el formulario si hay errores
        $_SESSION['datos_form'] = compact('nombre', 'apellidos', 'email');

        $errores = RegistroRequest::validar([
            'nombre' => $nombre,
            'email'  => $email,
            'clave'  => $clave,
            'clave2' => $clave2,
        ]);

        $servicio = new UsuarioServicio();

        if (!$errores && $servicio->emailExiste($email)) {
            $errores[] = 'Ya existe una cuenta con ese email';
        }

        if ($errores) {
            Sesion::mensaje('error', implode('<br>', $errores));
            Sesion::redirigir('auth/registro');
        }

        // El servicio hashea la clave, genera el token y hace el INSERT
        $token = $servicio->registrar([
            'nombre'    => $nombre,
            'apellidos' => $apellidos,
            'email'     => $email,
            'clave'     => $clave,
        ]);

        // Si habia datos del formulario guardados en sesion de un intento fallido anterior, los borramos
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
            Sesion::mensaje('error', 'El enlace no es valido o ya se uso');
            Sesion::redirigir('auth/login');
        }

        Sesion::mensaje('ok', 'Cuenta activada. Ya puedes iniciar sesion.');
        Sesion::redirigir('auth/login');
    }

    // Muestra el formulario de login
    public function login(): void
    {
        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/auth/login.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    // Procesa el formulario de login
    public function procesarLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sesion::redirigir('auth/login');
        }

        $email = trim($_POST['email'] ?? '');
        $clave = $_POST['clave']      ?? '';

        $errores = LoginRequest::validar(['email' => $email, 'clave' => $clave]);
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

        // El servicio maneja la logica de crear o actualizar el usuario de Google
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
