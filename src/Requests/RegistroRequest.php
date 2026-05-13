<?php

namespace Requests;

class RegistroRequest
{
    /**
     * Sanea y valida los datos del formulario de registro.
     * Devuelve un array con 'errores' (array) y 'datos' (array saneado).
     *
     * Por que NO aplicamos htmlspecialchars a las claves?
     *   Las contrasenas se hashean con password_hash() antes de guardarse.
     *   Si aplicaramos htmlspecialchars, un usuario que pone ">" en su clave
     *   la guardaria como "&gt;" y luego no podria hacer login con la clave real.
     *   Por eso la clave se limpia solo con trim() y nada mas.
     */
    public static function validar(array $datos): array
    {
        $errores = [];

        // ── SANEAMIENTO ─────────────────────────────────────────────────────
        // htmlspecialchars: convierte < > " ' & en entidades HTML → evita XSS
        // trim: elimina espacios al inicio y al final
        $nombre    = htmlspecialchars(trim($datos['nombre']    ?? ''), ENT_QUOTES, 'UTF-8');
        $apellidos = htmlspecialchars(trim($datos['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email     = trim($datos['email'] ?? '');
        $clave     = trim($datos['clave']  ?? '');
        $clave2    = trim($datos['clave2'] ?? '');

        // ── VALIDACION DE NOMBRE ─────────────────────────────────────────────
        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio';
        } elseif (strlen($nombre) < 2) {
            $errores[] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($nombre) > 60) {
            $errores[] = 'El nombre no puede superar 60 caracteres';
        }

        // ── VALIDACION DE APELLIDOS (opcional, pero si se rellena se valida) ─
        if ($apellidos !== '' && strlen($apellidos) > 100) {
            $errores[] = 'Los apellidos no pueden superar 100 caracteres';
        }

        // ── VALIDACION DE EMAIL ──────────────────────────────────────────────
        if ($email === '') {
            $errores[] = 'El email es obligatorio';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // filter_var con FILTER_VALIDATE_EMAIL comprueba el formato RFC
            $errores[] = 'El email no tiene un formato valido';
        } elseif (strlen($email) > 150) {
            $errores[] = 'El email no puede superar 150 caracteres';
        }

        // Sanitizamos el email DESPUES de validar el formato
        // para no alterar el valor antes de la comprobacion con filter_var
        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

        // ── VALIDACION DE CLAVE ──────────────────────────────────────────────
        if ($clave === '') {
            $errores[] = 'La contrasena es obligatoria';
        } elseif (strlen($clave) < 8) {
            $errores[] = 'La contrasena debe tener al menos 8 caracteres';
        } elseif (strlen($clave) > 100) {
            $errores[] = 'La contrasena no puede superar 100 caracteres';
        } elseif (!preg_match('/[0-9]/', $clave)) {
            // Exigimos al menos un numero para que no sea demasiado debil
            $errores[] = 'La contrasena debe contener al menos un numero';
        }

        // ── CONFIRMACION DE CLAVE ────────────────────────────────────────────
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
                'clave'     => $clave,   // se hashea en el servicio, no aqui
            ],
        ];
    }
}
