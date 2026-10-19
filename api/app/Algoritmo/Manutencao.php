<?php

namespace App\Algoritmo;

class Manutencao
{
    /**
     * @param  list<array{net_kcal: int|float, already_in_lifestyle: bool}>  $sessoes
     */
    public static function kcal(?int $basal, ?string $rotina, array $sessoes = []): ?int
    {
        $base = GastoDaRotina::kcal($basal, $rotina);

        if ($base === null) {
            return null;
        }

        $extra = 0.0;

        foreach ($sessoes as $sessao) {
            if ($sessao['already_in_lifestyle']) {
                continue;
            }

            $extra += $sessao['net_kcal'];
        }

        return (int) round($base + $extra);
    }
}
