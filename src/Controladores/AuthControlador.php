<?php

namespace Controladores;

use Lib\Pagina;
use Lib\Sesion;
use Lib\Cesta;
use Lib\GoogleOAuth;
use Servicios\UsuarioServicio;
use Requests\RegistroRequest;
use Requests\LoginRequest;

class AuthControlador
{
    private UsuarioServicio $usuarioServicio;

    public function __construct()
    {
        $this->usuarioServicio = new UsuarioServicio();
    }

    public function login(): void
    {
        if (Sesion::logeado()) Sesion::redirigir('');
        Pagina::renderizar('auth/login');
    }

    public function procesarLogin(): void
    {
        $resultado = LoginRequest::validar($_POST);
        $usuario = $this->usuarioServicio->verificarCredenciales(
            $resultado['datos']['email'],
            $resultado['datos']['clave']
        );

        if (!$usuario || !$usuario->activado) {
            Sesion::mensaje('error', 'Credenciales incorrectas o cuenta no activada');
            Sesion::redirigir('auth/login');
        }

        Sesion::iniciar($usuario);
        Cesta::migrarAlLogin($usuario->id);
        Sesion::redirigir('');
    }

    public function loginGoogle(): void
    {
        $googleOAuth = new GoogleOAuth();
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        header('Location: ' . $googleOAuth->getAuthUrl($state));
        exit;
    }

    public function googleCallback(): void
    {
        $googleOAuth = new GoogleOAuth();

        if (isset($_GET['code'])) {
            $tokenData = $googleOAuth->intercambiarCode($_GET['code']);
            if (isset($tokenData['access_token'])) {
                $userInfo = $googleOAuth->getUserInfo($tokenData['access_token']);
                $usuario = $this->usuarioServicio->procesarLoginGoogle([
                    'email'     => $userInfo['email'],
                    'nombre'    => $userInfo['name'] ?? $userInfo['given_name'],
                    'google_id' => $userInfo['sub'] ?? $userInfo['id'],
                ]);
                if ($usuario) {
                    Sesion::iniciar($usuario);
                    Cesta::migrarAlLogin($usuario->id);
                    Sesion::mensaje('ok', 'Conectado con Google');
                    Sesion::redirigir('');
                }
            }
        }
        Sesion::mensaje('error', 'Fallo en la conexión con Google');
        Sesion::redirigir('auth/login');
    }

    public function registro(): void
    {
        Pagina::renderizar('auth/registro');
    }

    public function procesarRegistro(): void
    {
        $resultado = RegistroRequest::validar($_POST);

        if ($this->usuarioServicio->registrar($resultado['datos'])) {
            Sesion::mensaje('ok', 'Registro ok, activa tu email');
            Sesion::redirigir('auth/login');
        } else {
            // el email ya está registrado y activado 
            Sesion::mensaje('error', 'Este email ya está registrado. Inicia sesión o usa otro email.');
            Sesion::redirigir('auth/registro');
        }
    }

    public function confirmar($token = null): void
    {
        $this->usuarioServicio->activarCuenta((string) $token);
        Sesion::redirigir('auth/login');
    }

    public function logout(): void
    {
        Sesion::cerrar();
        Sesion::redirigir('');
    }

    public function olvideClave(): void
    {
        Pagina::renderizar('auth/olvide_clave');
    }

    public function procesarOlvideClave(): void
    {
        $email = trim($_POST['email'] ?? '');
        if (!$email) {
            Sesion::mensaje('error', 'Introduce tu email');
            Sesion::redirigir('auth/olvideClave');
        }

        // Siempre mostramos el mismo mensaje para no revelar si el email existe
        $this->usuarioServicio->solicitarReset($email);
        Sesion::mensaje('ok', 'Si el email existe, recibirás un enlace para restablecer tu contraseña');
        Sesion::redirigir('auth/login');
    }

    public function resetPassword($token = null): void
    {
        if (!$token || !$this->usuarioServicio->validarTokenReset($token)) {
            Sesion::mensaje('error', 'El enlace no es válido o ha expirado');
            Sesion::redirigir('auth/login');
        }

        Pagina::renderizar('auth/reset_password', compact('token'));
    }

    public function procesarResetPassword(): void
    {
        $token  = $_POST['token'] ?? '';
        $clave  = $_POST['clave'] ?? '';
        $clave2 = $_POST['clave2'] ?? '';

        if (!$token || !$clave || $clave !== $clave2) {
            Sesion::mensaje('error', 'Las contraseñas no coinciden o el enlace es inválido');
            Sesion::redirigir('auth/login');
        }

        if (strlen($clave) < 6) {
            Sesion::mensaje('error', 'La contraseña debe tener al menos 6 caracteres');
            Sesion::redirigir('auth/resetpassword/' . $token);
        }

        if ($this->usuarioServicio->restablecerClave($token, $clave)) {
            Sesion::mensaje('ok', 'Contraseña restablecida correctamente. Ya puedes iniciar sesión.');
        } else {
            Sesion::mensaje('error', 'El enlace no es válido o ha expirado');
        }

        Sesion::redirigir('auth/login');
    }
}