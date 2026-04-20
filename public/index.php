<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

echo "<h1>Proyecto inicializado correctamente</h1>";
echo "<p>Base de datos configurada: " . $_ENV['DB_NAME'] . "</p>";
echo "<p>Composer funcionando ✓</p>";
echo "<p>Variables de entorno cargadas ✓</p>";

phpinfo();