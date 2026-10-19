<?php

namespace App\Objetivo;

use App\Algoritmo\AlvoDaMeta;
use App\Algoritmo\FocoDeMicronutrientes;
use App\Algoritmo\Manutencao;
use App\Models\User;
use App\Perfil\Corpo;
use App\Perfil\Perfil;
use App\RegistroDeExercicio\Sessoes;

class Consulta
{
    /**
     * @return array{goal: string|null, goal_started_on?: string, micronutrients?: list<string>, hydration_reminder?: string, kcal?: int, maintenance_kcal?: int, protein_g?: int|float, fat_g?: int|float, carb_g?: int|float, carb_warning?: string}
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
            ...FocoDeMicronutrientes::de($goal),
        ];

        $corpo = Corpo::resposta($usuario);
        $manutencao = Manutencao::kcal(
            $corpo['gasto_repouso_kcal'] ?? null,
            Perfil::query()->find($usuario->id)?->lifestyle,
            Sessoes::liquidas($usuario->id, now()->toDateString()),
        );
        $alvo = AlvoDaMeta::de($goal, $manutencao, $corpo['weight_kg']);

        if ($alvo !== null) {
            $resposta += $alvo;
        }

        return $resposta;
    }
}
