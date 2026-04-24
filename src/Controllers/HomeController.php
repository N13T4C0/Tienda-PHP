<?php
namespace App\Controllers;

class HomeController {
    public function index() {
        $base_url = dirname($_SERVER['SCRIPT_NAME']);
        require_once __DIR__ . '/../../views/home/index.php';
    }
}