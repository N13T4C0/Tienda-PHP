<?php
namespace App\Controllers;

use App\Requests\RegistroRequest;
use App\Services\UsuarioService;

class AuthController{
    private $service;

    public function __construct(){
        $this->service = new UsuarioService();
    }

    public function registro(){
        $base_url = dirname($_SERVER['SCRIPT_NAME']);
        require_once __DIR__ . '/../../views/auth/registro.php';
    }

    public function guardarRegistro(){
        $request = new RegistroRequest();

        if(!$request->validar($_POST)){
            $_SESSION['errores'] = $request->getErrores();
            header('Location: /registro');
            exit;
        }

        $resultado = $this->service->registrar($_POST);
        
        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
            header('Location: /registro');
        } else {
            $_SESSION['success'] = 'Registro exitoso. Revisa tu email para confirmar.';
            header('Location: /login');
        }
        exit;
    }

    public function login() {
        $base_url = dirname($_SERVER['SCRIPT_NAME']);
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function autenticar() {
        $usuario = $this->service->autenticar($_POST['email'], $_POST['password']);
        
        if ($usuario) {
            $_SESSION['identity'] = $usuario;
            header('Location: /');
        } else {
            $_SESSION['error'] = 'Credenciales incorrectas';
            header('Location: /login');
        }
        exit;
    }

    public function logout() {
        unset($_SESSION['identity']);
        header('Location: /');
        exit;
    }
}