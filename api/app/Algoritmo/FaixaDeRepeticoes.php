<?php

namespace App\Algoritmo;

class FaixaDeRepeticoes
{
    /**
     * @return array{min: int, max: int}|null
     */
    public static function de(?string $meta, ?int $repeticoesAnteriores): ?array
    {
        if ($meta === 'hipertrofia') {
            return ['min' => 8, 'max' => 12];
        }

        if ($meta === 'forca') {
            if ($repeticoesAnteriores !== null && $repeticoesAnteriores <= 6) {
                return ['min' => 1, 'max' => 6];
            }

            return ['min' => 8, 'max' => 12];
        }

        return null;
    }
}
