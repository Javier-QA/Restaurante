<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'email' => 'Ingresa un correo electrónico válido.',
    'unique' => 'El valor de :attribute ya está registrado.',
    'in' => 'Selecciona un valor válido para :attribute.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El archivo :attribute debe tener al menos :min kilobytes.',
    ],
    'max' => [
        'string' => 'El campo :attribute no debe superar :max caracteres.',
        'numeric' => 'El campo :attribute no debe superar :max.',
        'array' => 'El campo :attribute no debe superar :max elementos.',
        'file' => 'El archivo :attribute no debe superar :max kilobytes.',
    ],
    'attributes' => ['name' => 'nombre', 'email' => 'correo electrónico', 'password' => 'contraseña', 'role' => 'rol'],
];
