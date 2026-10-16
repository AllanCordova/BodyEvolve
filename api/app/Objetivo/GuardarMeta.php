<?php

namespace App\Objetivo;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Put(
    path: '/api/objetivo',
    operationId: 'guardarMeta',
    summary: 'Save one goal for the current account',
    tags: ['Objetivo'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/MetaNova'),
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Goal saved. Calorie and nutrient targets are present only when age, sex, weight, and height exist.',
            content: new OA\JsonContent(ref: '#/components/schemas/Meta'),
        ),
        new OA\Response(response: 401, description: 'No session'),
        new OA\Response(response: 422, description: 'Missing goal, unknown goal, or more than one goal'),
    ],
)]
class GuardarMeta
{
    public function __invoke(Request $request): JsonResponse
    {
        $escolha = Escolha::doPedido($request);
        $usuario = $request->user();
        $meta = Meta::query()->find($usuario->id);
        $hoje = now()->toDateString();

        if ($meta === null) {
            Meta::query()->create([
                'user_id' => $usuario->id,
                'goal' => $escolha,
                'goal_started_on' => $hoje,
            ]);
        } else {
            if ($meta->goal !== $escolha) {
                $meta->goal_started_on = $hoje;
            }

            $meta->goal = $escolha;
            $meta->save();
        }

        return response()->json(Consulta::resposta($usuario));
    }
}
