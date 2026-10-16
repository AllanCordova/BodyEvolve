<?php

namespace App\Acesso;

use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/treino',
    operationId: 'abrirTreino',
    summary: 'Open training when a session exists',
    tags: ['Acesso'],
    responses: [
        new OA\Response(response: 204, description: 'Session is present'),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
#[OA\Get(
    path: '/api/comida',
    operationId: 'abrirComida',
    summary: 'Open food when a session exists',
    tags: ['Acesso'],
    responses: [
        new OA\Response(response: 204, description: 'Session is present'),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
#[OA\Get(
    path: '/api/alertas',
    operationId: 'abrirAlertas',
    summary: 'Open alerts when a session exists',
    tags: ['Acesso'],
    responses: [
        new OA\Response(response: 204, description: 'Session is present'),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class AreaProtegida
{
    public function __invoke(): Response
    {
        return response()->noContent();
    }
}
