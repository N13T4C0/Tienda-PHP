<?php
namespace Servicios;

use Repositorios\UsuarioRepositorio;


class UsuarioServicio
{
    private UsuarioRepositorio $repositorio;

    public function __construct()
    {
        $this->repositorio = new UsuarioRepositorio();
    }

    /**
     * Registra un usuario nuevo.
     * Hashea la clave y genera el token de activacion.
     * Devuelve el token para poder enviarlo por email.
     */
    public function registrar(array $datos): string
    {
        // Token aleatorio de 32 caracteres para el enlace de verificacion del email
        $token = bin2hex(random_bytes(16));

        $this->repositorio->insertar([
            'nombre'      => htmlspecialchars($datos['nombre']),
            'apellidos'   => htmlspecialchars($datos['apellidos'] ?? ''),
            'email'       => $datos['email'],
            // Nunca se guarda la clave en texto plano, BCRYPT la encripta de forma segura
            'clave'       => password_hash($datos['clave'], PASSWORD_BCRYPT),
            // El rol siempre es cliente, el usuario no puede elegirlo
            'rol'         => 'cliente',
            // La cuenta empieza desactivada hasta que el usuario verifique su email
            'activado'    => 0,
            'token_email' => $token,
        ]);

        // Se devuelve el token para que el controlador lo envie por email al usuario
        return $token;
    }

    /**
     * Comprueba si el email ya esta en uso.
     */
    public function emailExiste(string $email): bool
    {
        return $this->repositorio->encontrarPorEmail($email) !== null;
    }

    /**
     * Valida las credenciales de un usuario.
     * Devuelve el array del usuario si son correctas, null si no.
     */
    public function verificarCredenciales(string $email, string $clave): ?array
    {
        $usuario = $this->repositorio->encontrarPorEmail($email);

        if (!$usuario) {
            return null;
        }

        if (!password_verify($clave, $usuario['clave'])) {
            return null;
        }

        return $usuario;
    }

    /**
     * Activa la cuenta de un usuario a partir del token del email.
     * Devuelve el usuario activado o null si el token no es valido.
     */
    public function activarCuenta(string $token): ?array
    {
        $usuario = $this->repositorio->encontrarPorToken($token);

        if (!$usuario) {
            return null;
        }

        $this->repositorio->activar((int) $usuario['id']);
        return $usuario;
    }

    /**
     * Busca un usuario por su id.
     */
    public function obtenerPorId(int $id): ?array
    {
        return $this->repositorio->encontrarPorId($id);
    }

    /**
     * Devuelve todos los usuarios (para el panel admin).
     */
    public function listarTodos(): array
    {
        return $this->repositorio->obtenerTodos();
    }

    /**
     * Elimina un usuario.
     */
    public function eliminar(int $id): bool
    {
        return $this->repositorio->eliminar($id);
    }

    /**
     * Registra o actualiza un usuario que viene de Google OAuth.
     * Devuelve el array del usuario ya guardado.
     */
    public function procesarLoginGoogle(array $infoGoogle): array
    {
        $partes    = explode(' ', $infoGoogle['name'] ?? $infoGoogle['email'], 2);
        $nombre    = htmlspecialchars($partes[0]);
        $apellidos = htmlspecialchars($partes[1] ?? '');

        $idUsuario = $this->repositorio->guardarDesdeGoogle([
            'google_id' => $infoGoogle['sub'],
            'email'     => $infoGoogle['email'],
            'nombre'    => $nombre,
            'apellidos' => $apellidos,
            'avatar'    => $infoGoogle['picture'] ?? null,
        ]);

        return $this->repositorio->encontrarPorId($idUsuario);
    }
}
