<?php

namespace App\Algoritmo;

class AlvoDaMeta
{
    /**
     * @return array{kcal: int, protein_g: int|float, fat_g: int|float, carb_g: int|float}|null
     */
    public static function de(?string $meta, ?int $gastoRepousoKcal, ?float $pesoKg): ?array
    {
        if ($meta === null || $gastoRepousoKcal === null || $pesoKg === null || $pesoKg <= 0) {
            return null;
        }

        $regra = self::regras()[$meta] ?? null;

        if ($regra === null) {
            return null;
        }

        $kcal = (int) round($gastoRepousoKcal * $regra['fator']);
        $proteina = self::gramas($pesoKg * $regra['proteina']);
        $gordura = self::gramas($pesoKg * $regra['gordura']);

        if (isset($regra['gordura_minima'])) {
            $gordura = max($gordura, self::gramas($pesoKg * $regra['gordura_minima']));
        }

        $saldo = $kcal - ($proteina * 4) - ($gordura * 9);
        $carbDoSaldo = $saldo > 0 ? $saldo / 4 : 0.0;
        $carbPretendido = $regra['carboidrato'];
        $carb = $carbPretendido === null
            ? $carbDoSaldo
            : min($pesoKg * $carbPretendido, $carbDoSaldo);

        return [
            'kcal' => $kcal,
            'protein_g' => $proteina,
            'fat_g' => $gordura,
            'carb_g' => self::gramas($carb),
        ];
    }

    /**
     * @return array<string, array{fator: float, proteina: float, gordura: float, carboidrato: float|null, gordura_minima?: float}>
     */
    private static function regras(): array
    {
        return [
            'hipertrofia' => ['fator' => 1.15, 'proteina' => 2.0, 'gordura' => 1.0, 'carboidrato' => null],
            'emagrecimento' => ['fator' => 0.8, 'proteina' => 2.2, 'gordura' => 0.8, 'carboidrato' => null, 'gordura_minima' => 0.6],
            'forca' => ['fator' => 1.05, 'proteina' => 1.8, 'gordura' => 1.1, 'carboidrato' => 4.0],
            'resistencia' => ['fator' => 1.0, 'proteina' => 1.4, 'gordura' => 1.2, 'carboidrato' => 8.0],
        ];
    }

    private static function gramas(float $valor): int|float
    {
        $arredondado = round($valor, 2);

        if (abs($arredondado - (int) $arredondado) < 0.001) {
            return (int) round($arredondado);
        }

        return $arredondado;
    }
}
