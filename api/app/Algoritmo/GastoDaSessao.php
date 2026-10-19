<?php

namespace App\Algoritmo;

class GastoDaSessao
{
    public static function liquidas(float $base, float $pesoKg, int $minutos, float $fator): float
    {
        return round(($base - 1) * $pesoKg * ($minutos / 60) * $fator, 2);
    }
}
