<?php

namespace App\RegistroDeExercicio;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Pedido
{
    /**
     * @return array{modality_code: string, duration_min: int, intensity_code: string, already_in_lifestyle: bool, trained_on: string}
     */
    public static function daSessao(Request $request): array
    {
        /** @var array{modality_code: string, duration_min: int, intensity_code: string, already_in_lifestyle: bool, trained_on?: string} $dados */
        $dados = $request->validate([
            'modality_code' => ['required', 'string'],
            'duration_min' => ['required', 'integer', 'min:1'],
            'intensity_code' => ['required', 'string'],
            'already_in_lifestyle' => ['required', 'boolean'],
            'trained_on' => ['sometimes', 'date'],
        ], [
            'modality_code.required' => 'Escolha a modalidade.',
            'duration_min.required' => 'Informe os minutos.',
            'duration_min.integer' => 'Informe os minutos.',
            'duration_min.min' => 'Informe os minutos.',
            'intensity_code.required' => 'Escolha a intensidade.',
            'already_in_lifestyle.required' => 'Diga se essa hora já está na rotina.',
            'already_in_lifestyle.boolean' => 'Diga se essa hora já está na rotina.',
            'trained_on.date' => 'Informe o dia da sessão.',
        ]);

        $modalidade = Catalogo::modalidade($dados['modality_code']);

        if ($modalidade === null || ! in_array($modalidade['catalog'], ['cardio', 'sports'], true)) {
            throw ValidationException::withMessages([
                'modality_code' => 'A modalidade vem do cardio ou de um esporte.',
            ]);
        }

        if (Catalogo::fator($dados['modality_code'], $dados['intensity_code']) === null) {
            throw ValidationException::withMessages([
                'intensity_code' => 'A intensidade não existe nessa modalidade.',
            ]);
        }

        $dados['trained_on'] = $dados['trained_on'] ?? now()->toDateString();

        return $dados;
    }
}
