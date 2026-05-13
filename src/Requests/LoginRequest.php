<?php

namespace Requests;

class LoginRequest
{
    /**
     * Sanea y valida los datos del formulario de login.
     * Devuelve un array con 'errores' (array) y 'datos' (array saneado).
     *
     * Nota sobre la clave: igual que en RegistroRequest, NO se aplica
     * htmlspecialchars a la clave porque se compara contra el hash guardado
     * en la BD usando password_verify(). Alterarla aqui romperia el login.
     */
    public static function validar(array $datos): array
    {
        $errores = [];

        // ── SANEAMIENTO ──────────────────────────────────────────────────────
        $email = trim($datos['email'] ?? '');
        $clave = trim($datos['clave'] ?? '');

        // ── VALIDACION DE EMAIL ───────────────────────────────────────────────
        if ($email === '') {
            $errores[] = 'El email es obligatorio';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato valido';
        } elseif (strlen($email) > 150) {
            $errores[] = 'El email no puede superar 150 caracteres';
        }

        // Sanitizamos despues de validar para no alterar el valor antes del check
        $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

        // ── VALIDACION DE CLAVE ───────────────────────────────────────────────
        // En login solo comprobamos que no venga vacia.
        // No validamos longitud minima para no dar pistas a posibles atacantes
        // sobre los requisitos de la clave.
        if ($clave === '') {
            $errores[] = 'La contrasena es obligatoria';
        }

        return [
            'errores' => $errores,
            'datos'   => [
                'email' => $email,
                'clave' => $clave,
            ],
        ];
    }
}
