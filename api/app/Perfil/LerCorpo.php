<?php

namespace App\Perfil;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/perfil',
    operationId: 'lerCorpo',
    summary: 'Read the body saved on the current account',
    tags: ['Perfil'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Body for this account. Resting energy is present only when age, sex, weight, and height exist.',
            content: new OA\JsonContent(ref: '#/components/schemas/Corpo'),
        ),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class LerCorpo
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(Corpo::resposta($request->user()));
    }
}
