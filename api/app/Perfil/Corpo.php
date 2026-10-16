<?php

namespace App\Perfil;

use App\Algoritmo\GastoEmRepouso;
use App\Models\User;

class Corpo
{
    /**
     * @return array{age_years: int|null, sex: string|null, height_cm: int|null, weight_kg: float|null, gasto_repouso_kcal?: int}
     */
    public static function resposta(User $usuario): array
    {
        $perfil = Perfil::query()->find($usuario->id);
        $peso = RegistroDePeso::query()
            ->where('user_id', $usuario->id)
            ->orderByDesc('recorded_on')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        $idade = $perfil === null ? null : $perfil->age_years;
        $sexo = $perfil?->sex;
        $altura = $perfil === null ? null : $perfil->height_cm;
        $quilos = $peso === null ? null : $peso->weight_kg;

        $corpo = [
            'age_years' => $idade,
            'sex' => $sexo,
            'height_cm' => $altura,
            'weight_kg' => $quilos,
        ];

        $gasto = GastoEmRepouso::kcal($idade, $sexo, $altura, $quilos);

        if ($gasto !== null) {
            $corpo['gasto_repouso_kcal'] = $gasto;
        }

        return $corpo;
    }
}
