<?php

namespace App\RegistroDeExercicio;

class Sessoes
{
    /**
     * @return list<array{net_kcal: float, already_in_lifestyle: bool}>
     */
    public static function liquidas(string $usuarioId, string $dia): array
    {
        return Sessao::query()
            ->where('user_id', $usuarioId)
            ->whereDate('trained_on', $dia)
            ->get(['net_kcal', 'already_in_lifestyle'])
            ->map(fn (Sessao $sessao): array => [
                'net_kcal' => $sessao->net_kcal,
                'already_in_lifestyle' => $sessao->already_in_lifestyle,
            ])
            ->all();
    }
}
