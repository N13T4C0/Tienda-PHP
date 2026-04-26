<?php
namespace App\Controllers;

use App\Requests\RegistroRequest;
use App\Services\UsuarioService;

class AuthController{
    private $service;
    private $base_url;

    public function __construct(){
        $this->service = new UsuarioService();
        // Calcular la URL base correcta (quitando /public del path)
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $scriptDir = dirname($scriptName);
        // Si estamos en public/, quitar ese segmento
        if (basename($scriptDir) === 'public') {
            $this->base_url = dirname($scriptDir);
        } else {
            $this->base_url = $scriptDir;
        }
        // Normalizar: si es \ o /, dejar vacío
        if ($this->base_url === '/' || $this->base_url === '\\') {
            $this->base_url = '';
        }
    }

    public function registro(){
        require_once __DIR__ . '/../../views/auth/registro.php';
    }

    public function guardarRegistro(){
        $request = new RegistroRequest();

        if(!$request->validar($_POST)){
            $_SESSION['errores'] = $request->getErrores();
            header('Location: ' . $this->base_url . '/registro');
            exit;
        }

        $resultado = $this->service->registrar($_POST);
        
        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
            header('Location: ' . $this->base_url . '/registro');
        } else {
            $_SESSION['success'] = 'Registro exitoso. Revisa tu email para confirmar.';
            header('Location: ' . $this->base_url . '/login');
        }
        exit;
    }

    public function login() {
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function autenticar() {
        $resultado = $this->service->autenticar($_POST['email'], $_POST['password']);
        
        if (isset($resultado['error'])) {
            $_SESSION['error'] = $resultado['error'];
            header('Location: ' . $this->base_url . '/login');
        } else {
            // Guardar solo el objeto usuario en la sesión, no todo el array
            $_SESSION['identity'] = $resultado['usuario'];
            header('Location: ' . $this->base_url . '/');
        }
        exit;
    }

    public function logout() {
        unset($_SESSION['identity']);
        header('Location: ' . $this->base_url . '/');
        exit;
    }
}