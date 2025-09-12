<?php
namespace App\Validators;

class AuthValidator
{
    public static function validateRegister(array $data): array
    {
        $errors = [];

        $nombre = trim($data['nom_usuario'] ?? '');
        $correo = trim($data['correo'] ?? '');
        $pass   = $data['contrasena'] ?? '';
        $confirm= $data['confirm_contra'] ?? '';

        if (empty($nombre)) {
            $errors['nom_usuario'] = 'El nombre es obligatorio';
        } elseif (strlen($nombre) < 3) {
            $errors['nom_usuario'] = 'Debe tener mínimo 3 caracteres';
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = 'El correo ingresado no tiene un formato válido';
        }

        if (empty($pass) || empty($confirm)) {
            $errors['general'] = 'Las contraseñas son obligatorias';
        } elseif ($pass !== $confirm) {
            $errors['confirm_contra'] = 'Las contraseñas no coinciden';
        } elseif (!preg_match(
            '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/',
            $pass
        )) {
            $errors['contrasena'] = 'Debe tener al menos 6 caracteres, una mayúscula, una minúscula, un número y un símbolo';
        }

        return $errors;
    }

    public static function validateLogin(array $data): array
    {
        $errors = [];
        $correo = trim($data['correo'] ?? '');
        $pass   = $data['contrasena'] ?? '';

        if (empty($correo) || empty($pass)) {
            $errors['general'] = 'Todos los campos deben estar diligenciados.';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = 'Formato de correo inválido';
        }

        return $errors;
    }
}
