<?php
namespace App\Models;

class Usuario {
    public $id;
    public $nombre;
    public $apellidos;
    public $email;
    public $password;
    public $rol;
    public $confirmado;
    public $token;
    public $token_exp;
    public $created_at;
    public $updated_at;
}