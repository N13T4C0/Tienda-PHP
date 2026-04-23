<?php
namespace App\Repositories;

use Config\Database;
use App\Models\Usuario;
use PDO;

class UsuarioRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function crear(Usuario $usuario) {
        $sql = "INSERT INTO usuarios (nombre, apellidos, email, password, rol, confirmado, token, token_exp) 
                VALUES (:nombre, :apellidos, :email, :password, :rol, :confirmado, :token, :token_exp)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $usuario->nombre,
            ':apellidos' => $usuario->apellidos,
            ':email' => $usuario->email,
            ':password' => $usuario->password,
            ':rol' => $usuario->rol,
            ':confirmado' => $usuario->confirmado,
            ':token' => $usuario->token,
            ':token_exp' => $usuario->token_exp
        ]);
        
        return $this->db->lastInsertId();
    }

    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        return $stmt->fetchObject(Usuario::class);
    }

    public function buscarPorToken($token) {
        $sql = "SELECT * FROM usuarios WHERE token = :token AND token_exp > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        
        return $stmt->fetchObject(Usuario::class);
    }

    public function confirmarEmail($id) {
        $sql = "UPDATE usuarios SET confirmado = TRUE, token = NULL, token_exp = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function buscarPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetchObject(Usuario::class);
    }
}