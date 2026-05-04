<?php
/**
 * Instalación de usuarios de prueba
 */

require_once __DIR__ . '/../config/Conexion.php';

$bd = Conexion::abrir();

$usuarios = [
    ['nombre' => 'Admin',   'apellidos' => 'Tienda', 'email' => 'admin@netstore.com',   'clave' => 'admin123',   'rol' => 'admin'],
    ['nombre' => 'Cliente', 'apellidos' => 'Demo',   'email' => 'cliente@netstore.com', 'clave' => 'cliente123', 'rol' => 'cliente'],
];

echo "<h1>Instalación de netStore</h1>";
echo "<pre style='background:#eee;padding:1rem;'>";

foreach ($usuarios as $u) {
    $stmt = $bd->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $u['email']]);

    if ($stmt->fetch()) {
        echo "[SKIP] {$u['email']} ya existía\n";
        continue;
    }

    $bd->prepare(
        "INSERT INTO usuarios (nombre, apellidos, email, clave, rol, activado)
         VALUES (:n, :a, :e, :c, :r, 1)"
    )->execute([
        ':n' => $u['nombre'],
        ':a' => $u['apellidos'],
        ':e' => $u['email'],
        ':c' => password_hash($u['clave'], PASSWORD_BCRYPT),
        ':r' => $u['rol'],
    ]);

    echo "[OK] Creado {$u['email']} (clave: {$u['clave']}, rol: {$u['rol']})\n";
}

echo "</pre>";
echo "<p>Listo. <strong>borrra o renombra este fichero ahora</strong> por seguridad.</p>";
echo "<p><a href='./'>Ir a la tienda</a></p>";