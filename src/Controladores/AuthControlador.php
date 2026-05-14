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
}