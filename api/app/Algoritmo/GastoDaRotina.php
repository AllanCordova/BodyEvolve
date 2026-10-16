<?php

namespace App\Algoritmo;

class GastoDaRotina
{
    public static function kcal(?int $basal, ?string $rotina): ?int
    {
        $peso = self::pesos()[$rotina] ?? null;

        if ($basal === null || $peso === null) {
            return null;
        }

        return (int) round($basal * $peso);
    }

    /**
     * @return array<string, float>
     */
    private static function pesos(): array
    {
        return [
            'sedentaria' => 1.53,
            'moderada' => 1.76,
            'vigorosa' => 2.25,
        ];
    }
}
