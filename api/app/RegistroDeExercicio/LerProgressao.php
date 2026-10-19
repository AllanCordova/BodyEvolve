<?php

namespace App\RegistroDeExercicio;

use App\Algoritmo\Progressao;
use App\Objetivo\Meta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/treino/progressao',
    operationId: 'lerProgressao',
    summary: 'Compare the latest exposure of each exercise with the previous one',
    tags: ['Registro de exercício'],
    responses: [
        new OA\Response(response: 200, description: 'Progress, stall, or a load suggestion. The first exposure has no state.'),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class LerProgressao
{
    public function __invoke(Request $request): JsonResponse
    {
        $usuario = $request->user();
        $meta = Meta::query()->find($usuario->id)?->goal;
        $codigos = Lancamento::query()
            ->where('user_id', $usuario->id)
            ->distinct()
            ->pluck('exercise_code');

        $lista = [];

        foreach ($codigos as $codigo) {
            $exercicio = Exercicios::um($codigo);

            if ($exercicio === null) {
                continue;
            }

            $ultimos = Lancamento::query()
                ->where('user_id', $usuario->id)
                ->where('exercise_code', $codigo)
                ->orderByDesc('recorded_at')
                ->limit(2)
                ->get();

            $atual = $ultimos->first();
            $anterior = $ultimos->count() > 1 ? $ultimos->last() : null;
            $leitura = Progressao::de($meta, $anterior === null ? null : self::exposicao($anterior), self::exposicao($atual), $exercicio);

            $lista[] = [
                'exercise_code' => $codigo,
                'exercise_name' => $exercicio['name'],
                ...$leitura,
            ];
        }

        return response()->json($lista);
    }

    /**
     * @return array{reps_done: int, load_kg: float|null, variation: string|null}
     */
    private static function exposicao(Lancamento $lancamento): array
    {
        return [
            'reps_done' => $lancamento->reps_done,
            'load_kg' => $lancamento->load_kg,
            'variation' => $lancamento->variation,
        ];
    }
}
