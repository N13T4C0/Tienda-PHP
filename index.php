<?php
// Este archivo actúa como puente para evitar el listado de directorios
// y redirigir todas las peticiones a la carpeta 'public'

// Redirección permanente a la carpeta public manteniendo la URL o redirigiendo internamente
// Opción A: Redirección interna (Rewrite) si el .htaccess no lo captura bien
require __DIR__ . '/public/index.php';