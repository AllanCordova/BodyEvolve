<?php

namespace App\Algoritmo;

class GastoEmRepouso
{
    public static function kcal(?int $idade, ?string $sexo, ?int $alturaCm, ?float $pesoKg): ?int
    {
        if ($idade === null || $sexo === null || $alturaCm === null || $pesoKg === null) {
            return null;
        }

        if ($sexo !== 'masculino' && $sexo !== 'feminino') {
            return null;
        }

        $basal = (10 * $pesoKg) + (6.25 * $alturaCm) - (5 * $idade);

        return (int) round($sexo === 'masculino' ? $basal + 5 : $basal - 161);
    }
}
