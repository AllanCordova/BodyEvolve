<?php

namespace App\Perfil;

use App\Algoritmo\GastoDaRotina;
use App\Models\User;

class Rotina
{
    /**
     * @return array{lifestyle: string|null, gasto_da_rotina_kcal?: int}
     */
    public static function resposta(User $usuario): array
    {
        $lifestyle = Perfil::query()->find($usuario->id)?->lifestyle;

        if ($lifestyle === null) {
            return ['lifestyle' => null];
        }

        $resposta = ['lifestyle' => $lifestyle];
        $gasto = GastoDaRotina::kcal(Corpo::resposta($usuario)['gasto_repouso_kcal'] ?? null, $lifestyle);

        if ($gasto !== null) {
            $resposta['gasto_da_rotina_kcal'] = $gasto;
        }

        return $resposta;
    }
}
