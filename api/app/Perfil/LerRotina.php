<?php

namespace App\Perfil;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/perfil/rotina',
    operationId: 'lerRotina',
    summary: 'Read how active the day is for the current account',
    tags: ['Perfil'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Routine for this account. Expenditure is basal times the routine weight, and is present only when age, sex, weight, and height exist.',
            content: new OA\JsonContent(ref: '#/components/schemas/Rotina'),
        ),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class LerRotina
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(Rotina::resposta($request->user()));
    }
}
