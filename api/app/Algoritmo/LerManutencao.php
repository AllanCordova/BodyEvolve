<?php

namespace App\Algoritmo;

use App\Perfil\Corpo;
use App\Perfil\Perfil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/manutencao',
    operationId: 'lerManutencao',
    summary: 'Read maintenance calories for the current account',
    tags: ['Algoritmo'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Basal from Mifflin-St Jeor. Maintenance is basal times the routine weight, plus net kcal of today\'s sessions that are not already inside the routine. Maintenance is present only when the body and the routine exist.',
            content: new OA\JsonContent(ref: '#/components/schemas/Manutencao'),
        ),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class LerManutencao
{
    public function __invoke(Request $request): JsonResponse
    {
        $usuario = $request->user();
        $corpo = Corpo::resposta($usuario);
        $basal = $corpo['gasto_repouso_kcal'] ?? null;
        $lifestyle = Perfil::query()->find($usuario->id)?->lifestyle;

        $resposta = [
            'basal_kcal' => $basal,
            'lifestyle' => $lifestyle,
        ];

        $manutencao = Manutencao::kcal($basal, $lifestyle, self::sessoesDeHoje());

        if ($manutencao !== null) {
            $resposta['maintenance_kcal'] = $manutencao;
        }

        return response()->json($resposta);
    }

    /**
     * Sessions land with the training log. Until then the day has none.
     *
     * @return list<array{net_kcal: int|float, already_in_lifestyle: bool}>
     */
    private static function sessoesDeHoje(): array
    {
        return [];
    }
}
