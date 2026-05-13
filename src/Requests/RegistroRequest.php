<?php

namespace Requests;

class RegistroRequest
{
    // La clave no lleva htmlspecialchars porque se hashea directamente con password_hash
    public static function validar(array $datos): array
    {
        $errores = [];

        $nombre    = htmlspecialchars(trim($datos['nombre']    ?? ''), ENT_QUOTES, 'UTF-8');
        $apellidos = htmlspecialchars(trim($datos['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email     = trim($datos['email'] ?? '');
        $clave     = trim($datos['clave']  ?? '');
        $clave2    = trim($datos['clave2'] ?? '');

        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio';
        } elseif (strlen($nombre) < 2) {
            $errores[] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($nombre) > 60) {
            $errores[] = 'El nombre no puede superar 60 caracteres';
        }

        if ($apellidos !== '' && strlen($apellidos) > 100) {
            $errores[] = 'Los apellidos no pueden superar 100 caracteres';
        }

        if ($email === '') {
            $errores[] = 'El email es obligatorio';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato valido';
        } elseif (strlen($email) > 150) {
            $errores[] = 'El email no puede superar 150 caracteres';
        }

        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

        if ($clave === '') {
            $errores[] = 'La contrasena es obligatoria';
        } elseif (strlen($clave) < 8) {
            $errores[] = 'La contrasena debe tener al menos 8 caracteres';
        } elseif (!preg_match('/[0-9]/', $clave)) {
            $errores[] = 'La contrasena debe contener al menos un numero';
        }

        if ($clave !== '' && $clave2 === '') {
            $errores[] = 'Debes repetir la contrasena';
        } elseif ($clave !== $clave2) {
            $errores[] = 'Las contrasenas no coinciden';
        }

        return [
            'errores' => $errores,
            'datos'   => [
                'nombre'    => $nombre,
                'apellidos' => $apellidos,
                'email'     => $email,
                'clave'     => $clave,
            ],
        ];
    }
}
