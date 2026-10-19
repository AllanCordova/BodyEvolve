<?php

namespace App\Algoritmo;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/alertas',
    operationId: 'lerAlertas',
    summary: 'Read whether the day is on the route',
    tags: ['Algoritmo'],
    responses: [
        new OA\Response(response: 200, description: 'Alerts for today. A condition that is no longer true disappears.'),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class LerAlertas
{
    public function __invoke(Request $request): JsonResponse
    {
        $usuario = $request->user();
        $dia = now()->toDateString();
        $alertas = RotaDoDia::alertas($usuario, $dia);

        Alerta::query()->where('user_id', $usuario->id)->whereDate('alert_date', $dia)->delete();

        foreach ($alertas as $alerta) {
            Alerta::query()->create([
                'user_id' => $usuario->id,
                'alert_date' => $dia,
                'kind' => $alerta['kind'],
                'message' => $alerta['message'],
                'fails_goal' => $alerta['fails_goal'],
                'resolved' => false,
            ]);
        }

        return response()->json($alertas);
    }
}
