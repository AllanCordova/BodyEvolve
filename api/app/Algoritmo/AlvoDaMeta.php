<?php

namespace App\Algoritmo;

class AlvoDaMeta
{
    /**
     * @return array{kcal: int, maintenance_kcal: int, protein_g: int|float, fat_g: int|float, carb_g: int|float, carb_warning?: string}|null
     */
    public static function de(?string $meta, ?int $manutencaoKcal, ?float $pesoKg): ?array
    {
        if ($meta === null || $manutencaoKcal === null || $pesoKg === null || $pesoKg <= 0) {
            return null;
        }

        $regra = self::regras()[$meta] ?? null;

        if ($regra === null) {
            return null;
        }

        $kcal = (int) round($manutencaoKcal * $regra['fator']);
        $proteina = self::gramas($pesoKg * $regra['proteina']);
        $gordura = self::gramas($pesoKg * $regra['gordura']);
        $pisoDeGordura = self::gramas($pesoKg * $regra['gordura_minima']);

        if ($gordura < $pisoDeGordura) {
            $gordura = $pisoDeGordura;
        }

        $saldo = $kcal - ($proteina * 4) - ($gordura * 9);
        $carbDoSaldo = $saldo > 0 ? $saldo / 4 : 0.0;
        $carbPretendido = $regra['carboidrato'];
        $pedido = $carbPretendido === null ? null : $pesoKg * $carbPretendido;
        $carb = $pedido === null ? $carbDoSaldo : min($pedido, $carbDoSaldo);

        $alvo = [
            'kcal' => $kcal,
            'maintenance_kcal' => $manutencaoKcal,
            'protein_g' => $proteina,
            'fat_g' => $gordura,
            'carb_g' => self::gramas($carb),
        ];

        if ($pedido !== null && $pedido > $carbDoSaldo) {
            $alvo['carb_warning'] = 'O carboidrato pedido não coube no saldo. Ficou o que sobrou.';
        }

        return $alvo;
    }

    /**
     * @return array<string, array{fator: float, proteina: float, gordura: float, gordura_minima: float, carboidrato: float|null}>
     */
    private static function regras(): array
    {
        return [
            'hipertrofia' => ['fator' => 1.15, 'proteina' => 2.0, 'gordura' => 1.0, 'gordura_minima' => 0.0, 'carboidrato' => null],
            'emagrecimento' => ['fator' => 0.8, 'proteina' => 2.2, 'gordura' => 0.8, 'gordura_minima' => 0.6, 'carboidrato' => null],
            'forca' => ['fator' => 1.05, 'proteina' => 1.8, 'gordura' => 1.1, 'gordura_minima' => 0.0, 'carboidrato' => 4.0],
            'resistencia' => ['fator' => 1.0, 'proteina' => 1.4, 'gordura' => 1.2, 'gordura_minima' => 0.0, 'carboidrato' => 8.0],
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
