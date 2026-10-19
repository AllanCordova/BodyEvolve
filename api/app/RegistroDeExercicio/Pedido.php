<?php

namespace App\RegistroDeExercicio;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Pedido
{
    /**
     * @return array{modality_code: string, duration_min: int, intensity_code: string, already_in_lifestyle: bool, trained_on: string, exercises: list<array{exercise_code: string, sets_done: int, reps_done: int, load_kg: float|null, variation: string|null}>}
     */
    public static function daSessao(Request $request): array
    {
        /** @var array{modality_code: string, duration_min: int, intensity_code: string, already_in_lifestyle: bool, trained_on?: string, exercises?: list<array<string, mixed>>} $dados */
        $dados = $request->validate([
            'modality_code' => ['required', 'string'],
            'duration_min' => ['required', 'integer', 'min:1'],
            'intensity_code' => ['required', 'string'],
            'already_in_lifestyle' => ['required', 'boolean'],
            'trained_on' => ['sometimes', 'date'],
            'exercises' => ['sometimes', 'array'],
            'exercises.*.exercise_code' => ['required', 'string'],
            'exercises.*.sets_done' => ['required', 'integer', 'min:1'],
            'exercises.*.reps_done' => ['required', 'integer', 'min:1'],
            'exercises.*.load_kg' => ['nullable', 'numeric', 'gt:0'],
            'exercises.*.variation' => ['nullable', 'string', 'max:80'],
        ], [
            'modality_code.required' => 'Escolha a modalidade.',
            'duration_min.required' => 'Informe os minutos.',
            'duration_min.integer' => 'Informe os minutos.',
            'duration_min.min' => 'Informe os minutos.',
            'intensity_code.required' => 'Escolha a intensidade.',
            'already_in_lifestyle.required' => 'Diga se essa hora já está na rotina.',
            'already_in_lifestyle.boolean' => 'Diga se essa hora já está na rotina.',
            'trained_on.date' => 'Informe o dia da sessão.',
            'exercises.*.exercise_code.required' => 'Escolha o exercício no catálogo.',
            'exercises.*.sets_done.required' => 'Informe as séries.',
            'exercises.*.reps_done.required' => 'Informe as repetições.',
        ]);

        $modalidade = Catalogo::modalidade($dados['modality_code']);

        if ($modalidade === null) {
            throw ValidationException::withMessages([
                'modality_code' => 'A modalidade vem do catálogo.',
            ]);
        }

        if (Catalogo::fator($dados['modality_code'], $dados['intensity_code']) === null) {
            throw ValidationException::withMessages([
                'intensity_code' => 'A intensidade não existe nessa modalidade.',
            ]);
        }

        $exercicios = self::exercicios($modalidade, $dados['exercises'] ?? []);
        $dados['trained_on'] = $dados['trained_on'] ?? now()->toDateString();
        $dados['exercises'] = $exercicios;

        return $dados;
    }

    /**
     * @param  array{code: string, catalog: string, counts_as_strength: bool}  $modalidade
     * @param  list<array<string, mixed>>  $enviados
     * @return list<array{exercise_code: string, sets_done: int, reps_done: int, load_kg: float|null, variation: string|null}>
     */
    private static function exercicios(array $modalidade, array $enviados): array
    {
        if ($modalidade['code'] === 'circuito' && $enviados !== []) {
            throw ValidationException::withMessages([
                'exercises' => 'Burpee, polichinelo e mountain climber não contam como treino de força.',
            ]);
        }

        if ($modalidade['catalog'] === 'strong' && $enviados === []) {
            throw ValidationException::withMessages([
                'exercises' => 'Escolha o exercício no catálogo.',
            ]);
        }

        if (! $modalidade['counts_as_strength'] && $enviados !== []) {
            throw ValidationException::withMessages([
                'exercises' => 'Essa modalidade não leva exercício de força.',
            ]);
        }

        $limpos = [];

        foreach ($enviados as $indice => $enviado) {
            $codigo = $enviado['exercise_code'];

            if (Exercicios::um($codigo) === null) {
                throw ValidationException::withMessages([
                    "exercises.$indice.exercise_code" => 'Escolha o exercício no catálogo.',
                ]);
            }

            $carga = $enviado['load_kg'] ?? null;
            $variacao = $enviado['variation'] ?? null;

            if ($carga === null && ($variacao === null || $variacao === '')) {
                throw ValidationException::withMessages([
                    "exercises.$indice.load_kg" => 'Informe a carga ou a variação.',
                ]);
            }

            $limpos[] = [
                'exercise_code' => $codigo,
                'sets_done' => (int) $enviado['sets_done'],
                'reps_done' => (int) $enviado['reps_done'],
                'load_kg' => $carga === null ? null : (float) $carga,
                'variation' => $variacao === '' ? null : $variacao,
            ];
        }

        return $limpos;
    }
}
