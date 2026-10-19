<?php

namespace App\Algoritmo;

class FocoDeMicronutrientes
{
    /**
     * @return array{micronutrients: list<string>, hydration_reminder?: string}
     */
    public static function de(?string $meta): array
    {
        $foco = self::focos()[$meta] ?? null;

        if ($foco === null) {
            return ['micronutrients' => []];
        }

        $resposta = ['micronutrients' => $foco['micronutrients']];

        if ($foco['hydration']) {
            $resposta['hydration_reminder'] = 'Lembre de se hidratar.';
        }

        return $resposta;
    }

    /**
     * @return array<string, array{micronutrients: list<string>, hydration: bool}>
     */
    private static function focos(): array
    {
        return [
            'hipertrofia' => [
                'micronutrients' => ['Zinco', 'Magnésio', 'Vitamina D3'],
                'hydration' => false,
            ],
            'emagrecimento' => [
                'micronutrients' => ['Cálcio', 'Ferro', 'Complexo B'],
                'hydration' => false,
            ],
            'forca' => [
                'micronutrients' => ['Cálcio', 'Vitamina D3', 'Magnésio'],
                'hydration' => false,
            ],
            'resistencia' => [
                'micronutrients' => ['Sódio', 'Potássio', 'Magnésio', 'Ferro'],
                'hydration' => true,
            ],
        ];
    }
}
