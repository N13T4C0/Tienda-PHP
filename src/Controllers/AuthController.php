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
        $base_url = dirname($_SERVER['SCRIPT_NAME']);
        $request = new RegistroRequest();

        if(!$request->validar($_POST)){
            $_SESSION['errores'] = $request->getErrores();
            header('Location: ' . $base_url . '/registro');
            exit;
        }

        $resultado = $this->service->registrar($_POST);
        
        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
            header('Location: ' . $base_url . '/registro');
        } else {
            $_SESSION['success'] = 'Registro exitoso. Revisa tu email para confirmar.';
            header('Location: ' . $base_url . '/login');
        }
        exit;
    }

    public function login() {
        $base_url = dirname($_SERVER['SCRIPT_NAME']);
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function autenticar() {
        $resultado = $this->service->autenticar($_POST['email'], $_POST['password']);
        
        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
            header('Location: ' . $base_url . '/login');
        } else {
            // Guardar solo el objeto usuario en la sesión, no todo el array
            $_SESSION['identity'] = $resultado['usuario'];
            header('Location: ' . $base_url . '/');
        }
        exit;
    }

    public function logout() {
        unset($_SESSION['identity']);
        header('Location: ' . $base_url . '/');
        exit;
    }
}