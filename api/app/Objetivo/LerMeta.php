<?php

namespace App\Objetivo;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/objetivo',
    operationId: 'lerMeta',
    summary: 'Read the goal saved on the current account',
    tags: ['Objetivo'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Goal for this account. Calorie and nutrient targets are present only when the body is complete.',
            content: new OA\JsonContent(ref: '#/components/schemas/Meta'),
        ),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class LerMeta
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(Consulta::resposta($request->user()));
    }
}
