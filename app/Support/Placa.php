<?php

namespace App\Support;

class Placa
{
    public static function normalizar(string $placa): string
    {
        return strtoupper(preg_replace('/[\s\-]+/u', '', $placa) ?? '');
    }

    public static function esValida(string $normalizada): bool
    {
        return (bool) preg_match('/^[A-Z0-9]{6,8}$/', $normalizada);
    }
}
