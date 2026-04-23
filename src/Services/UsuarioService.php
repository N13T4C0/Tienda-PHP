<?php
namespace App\Services;

use App\Repositories\UsuarioRepository;
use App\Models\Usuario;
use Firebase\JWT\JWT;

class UsuarioService {
    private $repository;
    private $emailService;

    public function __construct() {
        $this->repository = new UsuarioRepository();
        $this->emailService = new EmailService();
    }

    public function registrar($datos) {
        // Verificar si ya existe
        if ($this->repository->buscarPorEmail($datos['email'])) {
            return ['error' => 'El email ya está registrado'];
        }

        // Crear usuario
        $usuario = new Usuario();
        $usuario->nombre = htmlspecialchars(trim($datos['nombre']));
        $usuario->apellidos = htmlspecialchars(trim($datos['apellidos']));
        $usuario->email = htmlspecialchars(trim($datos['email']));
        $usuario->password = password_hash($datos['password'], PASSWORD_BCRYPT);
        $usuario->rol = 'usuario';
        $usuario->confirmado = false;
        
        // Generar token JWT
        $payload = [
            'email' => $usuario->email,
            'exp' => time() + (60 * 60 * 24) // 24 horas
        ];
        $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
        $usuario->token = $token;
        $usuario->token_exp = date('Y-m-d H:i:s', $payload['exp']);

        $id = $this->repository->crear($usuario);
        
        // Enviar email de confirmación
        $this->emailService->enviarConfirmacionRegistro($usuario, $token);
        
        return ['success' => true, 'id' => $id];
    }

    public function autenticar($email, $password) {
        $usuario = $this->repository->buscarPorEmail($email);
        
        if (!$usuario) {
            return ['error' => 'Credenciales incorrectas'];
        }

        if (!$usuario->confirmado) {
            return ['error' => 'Debes confirmar tu email antes de iniciar sesión'];
        }

        if (!password_verify($password, $usuario->password)) {
            return ['error' => 'Credenciales incorrectas'];
        }

        return ['success' => true, 'usuario' => $usuario];
    }

    public function confirmarEmail($token) {
        $usuario = $this->repository->buscarPorToken($token);
        
        if (!$usuario) {
            return ['error' => 'Token inválido o expirado'];
        }

        $this->repository->confirmarEmail($usuario->id);
        
        return ['success' => true];
    }
}