<?php

namespace App\RegistroDeExercicio;

use App\Algoritmo\FaixaDeRepeticoes;
use App\Objetivo\Meta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/treino/exercicios',
    operationId: 'lerExercicios',
    summary: 'List strength exercises and the repetition range of the current goal',
    tags: ['Registro de exercício'],
    responses: [
        new OA\Response(response: 200, description: 'Exercise catalog. rep_min and rep_max appear for hypertrophy and strength.'),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class LerExercicios
{
    public function __invoke(Request $request): JsonResponse
    {
        $usuario = $request->user();
        $meta = Meta::query()->find($usuario->id)?->goal;

        $lista = array_map(function (array $exercicio) use ($usuario, $meta): array {
            $anterior = Lancamento::query()
                ->where('user_id', $usuario->id)
                ->where('exercise_code', $exercicio['code'])
                ->orderByDesc('recorded_at')
                ->first();
            $faixa = FaixaDeRepeticoes::de($meta, $anterior?->reps_done);

            if ($faixa !== null) {
                $exercicio['rep_min'] = $faixa['min'];
                $exercicio['rep_max'] = $faixa['max'];
            }

            return $exercicio;
        }, Exercicios::todos());

        return response()->json($lista);
    }
}
