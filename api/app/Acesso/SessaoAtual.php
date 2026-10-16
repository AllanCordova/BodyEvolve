<?php

namespace App\Acesso;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/acesso/sessao',
    operationId: 'sessaoAtual',
    summary: 'Read the account of the current session',
    tags: ['Acesso'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Current account',
            content: new OA\JsonContent(ref: '#/components/schemas/Sessao'),
        ),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class SessaoAtual
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $usuario */
        $usuario = $request->user();

        return response()->json(Conta::resposta($usuario));
    }
}
