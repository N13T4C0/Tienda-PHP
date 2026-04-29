<?php
/**
 * Script de instalacion (uso unico).
 *
 * - Crea (si no existen) los usuarios de prueba con clave hasheada con bcrypt.
 * - Hay que ejecutarlo UNA sola vez tras importar el SQL.
 *   URL: http://localhost/ProyectoTiendaPHP/public/instalar.php
 *
 * Despues conviene RENOMBRARLO o BORRARLO para no dejarlo accesible.
 */

require_once __DIR__ . '/../app/config/conexion.php';

$bd = Conexion::abrir();

$usuarios = [
    [
        'nombre'    => 'Admin',
        'apellidos' => 'Tienda',
        'email'     => 'admin@tiendaphp.com',
        'clave'     => 'admin123',
        'rol'       => 'admin',
    ],
    [
        'nombre'    => 'Cliente',
        'apellidos' => 'Demo',
        'email'     => 'cliente@tiendaphp.com',
        'clave'     => 'cliente123',
        'rol'       => 'cliente',
    ],
];

echo "<h1>Instalacion de TiendaPHP</h1>";
echo "<pre style='background:#eee;padding:1rem;'>";

foreach ($usuarios as $u) {
    $stmt = $bd->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $u['email']]);
    if ($stmt->fetch()) {
        echo "[SKIP] {$u['email']} ya existia\n";
        continue;
    }

    $hash = password_hash($u['clave'], PASSWORD_BCRYPT);
    $ins = $bd->prepare(
        "INSERT INTO usuarios (nombre, apellidos, email, clave, rol, activado)
         VALUES (:n, :a, :e, :c, :r, 1)"
    );
    $ins->execute([
        ':n' => $u['nombre'],
        ':a' => $u['apellidos'],
        ':e' => $u['email'],
        ':c' => $hash,
        ':r' => $u['rol'],
    ]);
    echo "[OK]   Creado {$u['email']} (clave: {$u['clave']}, rol: {$u['rol']})\n";
}

echo "</pre>";
echo "<p>Listo. <strong>Borra o renombra este fichero ahora</strong> por seguridad.</p>";
echo "<p><a href='./'>Ir a la tienda</a></p>";
