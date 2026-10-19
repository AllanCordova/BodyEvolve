<?php

namespace App\Algoritmo;

class Progressao
{
    /**
     * @param  array{reps_done: int, load_kg: float|null, variation: string|null}|null  $anterior
     * @param  array{reps_done: int, load_kg: float|null, variation: string|null}  $atual
     * @param  array{multi_joint: bool, large_muscle: bool}  $exercicio
     * @return array{state: string|null, state_label: string|null, message: string|null, suggested_load_kg: float|null}
     */
    public static function de(?string $meta, ?array $anterior, array $atual, array $exercicio): array
    {
        $vazio = ['state' => null, 'state_label' => null, 'message' => null, 'suggested_load_kg' => null];

        if ($meta !== 'hipertrofia' && $meta !== 'forca') {
            return $vazio;
        }

        if (self::abaixoDoMinimo($meta, $atual['reps_done'])) {
            return [
                'state' => null,
                'state_label' => null,
                'message' => 'Reduza a carga até o mínimo da faixa voltar.',
                'suggested_load_kg' => null,
            ];
        }

        $faixaAtual = self::faixa($meta, $atual['reps_done']);

        if ($anterior === null || $faixaAtual === null) {
            return $vazio;
        }

        $faixaAnterior = self::faixa($meta, $anterior['reps_done']);

        if ($faixaAnterior === null || $faixaAnterior !== $faixaAtual) {
            return $vazio;
        }

        $mesmaCarga = self::mesmaCarga($anterior, $atual);
        $cargaSubiu = self::cargaMaior($anterior, $atual);
        $repsSubiram = $atual['reps_done'] > $anterior['reps_done'];
        $variacaoNova = ($atual['variation'] ?? null) !== null
            && ($atual['variation'] ?? null) !== ($anterior['variation'] ?? null);
        $dentro = $atual['reps_done'] >= $faixaAtual[0] && $atual['reps_done'] <= $faixaAtual[1];

        $estado = null;

        if ($dentro && ($cargaSubiu || $variacaoNova)) {
            $estado = 'progrediu';
        } elseif ($dentro && $mesmaCarga && $repsSubiram) {
            $estado = 'a_caminho';
        } elseif ($mesmaCarga && ! $repsSubiram && ! $variacaoNova) {
            $estado = 'estagnado';
        }

        $sugerida = null;
        $mensagem = null;
        $noTeto = $mesmaCarga
            && $anterior['reps_done'] === $faixaAtual[1]
            && $atual['reps_done'] === $faixaAtual[1]
            && $atual['load_kg'] !== null;

        if ($noTeto) {
            $percentual = $exercicio['multi_joint'] && $exercicio['large_muscle'] ? 1.10 : 1.02;
            $sugerida = round($atual['load_kg'] * $percentual, 2);
            $mensagem = 'Suba a carga.';
        }

        return [
            'state' => $estado,
            'state_label' => self::rotulos()[$estado] ?? null,
            'message' => $mensagem,
            'suggested_load_kg' => $sugerida,
        ];
    }

    /**
     * @return array{0: int, 1: int}|null
     */
    private static function faixa(string $meta, int $reps): ?array
    {
        if ($meta === 'hipertrofia') {
            return ($reps >= 8 && $reps <= 12) ? [8, 12] : null;
        }

        if ($reps >= 1 && $reps <= 6) {
            return [1, 6];
        }

        if ($reps >= 8 && $reps <= 12) {
            return [8, 12];
        }

        return null;
    }

    private static function abaixoDoMinimo(string $meta, int $reps): bool
    {
        if ($meta === 'hipertrofia') {
            return $reps < 8;
        }

        return $reps > 6 && $reps < 8;
    }

    /**
     * @param  array{load_kg: float|null, variation: string|null}  $anterior
     * @param  array{load_kg: float|null, variation: string|null}  $atual
     */
    private static function mesmaCarga(array $anterior, array $atual): bool
    {
        return $anterior['load_kg'] !== null
            && $atual['load_kg'] !== null
            && abs($anterior['load_kg'] - $atual['load_kg']) < 0.001
            && ($anterior['variation'] ?? null) === ($atual['variation'] ?? null);
    }

    /**
     * @param  array{load_kg: float|null}  $anterior
     * @param  array{load_kg: float|null}  $atual
     */
    private static function cargaMaior(array $anterior, array $atual): bool
    {
        return $anterior['load_kg'] !== null
            && $atual['load_kg'] !== null
            && $atual['load_kg'] > $anterior['load_kg'];
    }

    /**
     * @return array<string, string>
     */
    private static function rotulos(): array
    {
        return [
            'progrediu' => 'Progrediu',
            'a_caminho' => 'A caminho',
            'estagnado' => 'Estagnado',
        ];
    }
}
