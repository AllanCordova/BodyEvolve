<?php

namespace App\Perfil;

use Illuminate\Http\Request;

class Medidas
{
    /**
     * @return array{age_years: int, sex: string, height_cm: int, weight_kg: float}
     */
    public static function doPedido(Request $request): array
    {
        /** @var array{age_years: int, sex: string, height_cm: int, weight_kg: float|int|string} $dados */
        $dados = $request->validate([
            'age_years' => ['required', 'integer', 'min:1'],
            'sex' => ['required', 'in:masculino,feminino'],
            'height_cm' => ['required', 'integer', 'min:1'],
            'weight_kg' => ['required', 'numeric', 'gt:0'],
        ], self::mensagens());

        $dados['weight_kg'] = (float) $dados['weight_kg'];

        return $dados;
    }

    /**
     * @return array<string, string>
     */
    private static function mensagens(): array
    {
        return [
            'age_years.required' => 'Informe a idade.',
            'age_years.integer' => 'Informe a idade em anos.',
            'age_years.min' => 'Informe a idade em anos.',
            'sex.required' => 'Informe o sexo biológico.',
            'sex.in' => 'O sexo biológico é masculino ou feminino.',
            'height_cm.required' => 'Informe a altura.',
            'height_cm.integer' => 'Informe a altura em centímetros.',
            'height_cm.min' => 'Informe a altura em centímetros.',
            'weight_kg.required' => 'Informe o peso.',
            'weight_kg.numeric' => 'Informe o peso em quilos.',
            'weight_kg.gt' => 'Informe o peso em quilos.',
        ];
    }
}
