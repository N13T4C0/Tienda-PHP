<?php
/**
 * Controlador de autenticacion: registro, login, logout y confirmacion email.
 */
class AuthControlador
{
    // -------------------- REGISTRO --------------------

    /** Muestra el formulario de registro */
    public function registro(): void
    {
        $datosPrevios = $_SESSION['datos_form'] ?? [];
        unset($_SESSION['datos_form']);

        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/auth/registro.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    /** Procesa el formulario de registro */
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

        // Mantenemos los datos rellenados (menos la clave)
        $_SESSION['datos_form'] = compact('nombre','apellidos','email');

        // Validaciones basicas
        $errores = [];
        if ($nombre === '')                                  $errores[] = 'El nombre es obligatorio';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))      $errores[] = 'El email no tiene un formato valido';
        if (strlen($clave) < 6)                              $errores[] = 'La clave debe tener al menos 6 caracteres';
        if ($clave !== $clave2)                              $errores[] = 'Las claves no coinciden';

        $modelo = new Usuario();
        if (!$errores && $modelo->buscarPorEmail($email)) {
            $errores[] = 'Ya existe una cuenta con ese email';
        }

        if ($errores) {
            Sesion::mensaje('error', implode('<br>', $errores));
            Sesion::redirigir('auth/registro');
        }

        // Generamos un token aleatorio para confirmar el email
        $token = bin2hex(random_bytes(16));

        $idNuevo = $modelo->registrar([
            'nombre'      => htmlspecialchars($nombre),
            'apellidos'   => htmlspecialchars($apellidos),
            'email'       => $email,
            'clave'       => password_hash($clave, PASSWORD_BCRYPT),
            'rol'         => 'cliente',
            'activado'    => 0,
            'token_email' => $token,
        ]);

        // Limpiamos el form y enviamos email
        unset($_SESSION['datos_form']);
        EnvioMail::confirmacionRegistro($email, $nombre, $token);

        Sesion::mensaje('ok',
            'Registro completado revisa tu mail');
        Sesion::redirigir('auth/login');
    }

    /** Confirma el email a traves del token */
    public function confirmar($token = null): void
    {
        if (!$token) {
            Sesion::mensaje('error', 'Token no valido');
            Sesion::redirigir('auth/login');
        }
        $modelo  = new Usuario();
        $usuario = $modelo->buscarPorToken($token);
        if (!$usuario) {
            Sesion::mensaje('error', 'El enlace no es valido o ya se uso');
            Sesion::redirigir('auth/login');
        }
        $modelo->activarCuenta((int) $usuario['id']);
        Sesion::mensaje('ok', 'Cuenta activada. Ya puedes iniciar sesion.');
        Sesion::redirigir('auth/login');
    }

    // -------------------- LOGIN --------------------

    /** Muestra el formulario de login */
    public function login(): void
    {
        require APP . '/Vistas/comunes/cabecera.php';
        require APP . '/Vistas/auth/login.php';
        require APP . '/Vistas/comunes/pie.php';
    }

    /** Procesa el formulario de login */
    public function procesarLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Sesion::redirigir('auth/login');
        }

        $email = trim($_POST['email'] ?? '');
        $clave = $_POST['clave']      ?? '';

        $modelo  = new Usuario();
        $usuario = $modelo->buscarPorEmail($email);

        if (!$usuario || !password_verify($clave, $usuario['clave'])) {
            Sesion::mensaje('error', 'Credenciales incorrectas');
            Sesion::redirigir('auth/login');
        }
        if (!$usuario['activado']) {
            Sesion::mensaje('error', 'Tu cuenta aun no esta activada');
            Sesion::redirigir('auth/login');
        }

        Sesion::iniciar($usuario);
        Sesion::mensaje('ok', 'Bienvenido, ' . $usuario['nombre']);
        Sesion::redirigir('');
    }

    /** Cierra sesion */
    public function logout(): void
    {
        Sesion::cerrar();
        Sesion::mensaje('info', 'Sesion cerrada correctamente');
        Sesion::redirigir('');
    }

    public function loginGoogle(): void
{
    $google = $this->crearClienteGoogle();
    $state  = bin2hex(random_bytes(16));

    $_SESSION['oauth_state'] = $state;

    header('Location: ' . $google->getAuthUrl($state));
    exit;
}

public function googleCallback(): void
{
    // 1. Parámetros presentes
    if (!isset($_GET['code'], $_GET['state'])) {
        Sesion::mensaje('error', 'Respuesta de Google incorrecta');
        Sesion::redirigir('auth/login');
    }

    // 2. Verificar state (CSRF)
    if (!hash_equals($_SESSION['oauth_state'] ?? '', $_GET['state'])) {
        unset($_SESSION['oauth_state']);
        Sesion::mensaje('error', 'Error de seguridad. Intentalo de nuevo');
        Sesion::redirigir('auth/login');
    }
    unset($_SESSION['oauth_state']);

    $google = $this->crearClienteGoogle();

    // 3. Intercambiar code por token
    $tokenData = $google->intercambiarCode($_GET['code']);

    if (isset($tokenData['error']) || empty($tokenData['access_token'])) {
        Sesion::mensaje('error', 'Google rechazó la solicitud');
        Sesion::redirigir('auth/login');
    }

    // 4. Obtener datos del usuario
    $info = $google->getUserInfo($tokenData['access_token']);

    if (empty($info['email']) || !($info['email_verified'] ?? false)) {
        Sesion::mensaje('error', 'No se pudo verificar tu cuenta de Google');
        Sesion::redirigir('auth/login');
    }

    // 5. Separar nombre y apellidos
    $partes    = explode(' ', $info['name'] ?? $info['email'], 2);
    $nombre    = htmlspecialchars($partes[0]);
    $apellidos = htmlspecialchars($partes[1] ?? '');

    // 6. Crear o actualizar en BD
    $modelo = new Usuario();
    $idUser = $modelo->registrarOActualizarGoogle([
        'google_id' => $info['sub'],   // Google usa "sub" como ID único
        'email'     => $info['email'],
        'nombre'    => $nombre,
        'apellidos' => $apellidos,
        'avatar'    => $info['picture'] ?? null,
    ]);

    $usuario = $modelo->obtenerUno($idUser);

    // 7. Iniciar sesión
    Sesion::iniciar($usuario);
    Sesion::mensaje('ok', 'Bienvenido, ' . $usuario['nombre']);
    Sesion::redirigir('');
}

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
