<?php

namespace App\Objetivo;

use App\Algoritmo\AlvoDaMeta;
use App\Models\User;
use App\Perfil\Corpo;

class Consulta
{
    /**
     * @return array{goal: string|null, goal_started_on?: string, kcal?: int, protein_g?: int|float, fat_g?: int|float, carb_g?: int|float}
     */
    public static function resposta(User $usuario): array
    {
        $meta = Meta::query()->find($usuario->id);
        $goal = $meta?->goal;

        if ($goal === null) {
            return ['goal' => null];
        }

        $resposta = [
            'goal' => $goal,
            'goal_started_on' => $meta->goal_started_on->toDateString(),
        ];

        $corpo = Corpo::resposta($usuario);
        $alvo = AlvoDaMeta::de($goal, $corpo['gasto_repouso_kcal'] ?? null, $corpo['weight_kg']);

        if ($alvo !== null) {
            $resposta += $alvo;
        }

        return $resposta;
    }
}
