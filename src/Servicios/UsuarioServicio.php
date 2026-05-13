<?php

namespace Servicios;

use Repositorios\UsuarioRepositorio;
use Modelos\Usuario;
use Lib\EnvioMail;

class UsuarioServicio
{
    private UsuarioRepositorio $repositorio;

    public function __construct()
    {
        $this->repositorio = new UsuarioRepositorio();
    }

    /**
     * Lógica completa de registro: Hash, Token, DB y Email
     */
    public function registrar(array $datos): bool
    {
        // 1. Generar token de seguridad
        $token = bin2hex(random_bytes(16));

        // 2. Preparar datos (Hash de clave y saneo)
        $datosParaDB = [
            'nombre'      => htmlspecialchars($datos['nombre']),
            'apellidos'   => htmlspecialchars($datos['apellidos'] ?? ''),
            'email'       => $datos['email'],
            'clave'       => password_hash($datos['clave'], PASSWORD_BCRYPT),
            'rol'         => 'cliente',
            'activado'    => 0,
            'token_email' => $token
        ];

        // 3. Guardar en DB
        $id = $this->repositorio->insertar($datosParaDB);

        if ($id > 0) {
            // 4. Enviar el mail (Mantenemos tu lógica de envío)
            EnvioMail::confirmacionRegistro($datos['email'], $datos['nombre'], $token);
            return true;
        }

        return false;
    }

    public function emailExiste(string $email): bool
    {
        return $this->repositorio->encontrarPorEmail($email) !== null;
    }

    public function verificarCredenciales(string $email, string $clave): ?Usuario
    {
        $usuario = $this->repositorio->encontrarPorEmail($email);

        if ($usuario && password_verify($clave, $usuario->clave)) {
            return $usuario;
        }
        return null;
    }

    public function activarCuenta(string $token): ?Usuario
    {
        $usuario = $this->repositorio->encontrarPorToken($token);

        if ($usuario) {
            $this->repositorio->activar($usuario->id);
            return $usuario;
        }
        return null;
    }

    public function listarTodos(): array
    {
        return $this->repositorio->obtenerTodos();
    }

    public function contarTotales(): int
    {
        return $this->repositorio->contarTodos();
    }

    public function eliminar(int $id): bool
    {
        return $this->repositorio->eliminar($id);
    }

    public function procesarLoginGoogle(array $infoGoogle): Usuario
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
