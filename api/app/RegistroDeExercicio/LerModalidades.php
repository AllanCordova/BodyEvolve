<?php

namespace App\RegistroDeExercicio;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/treino/modalidades',
    operationId: 'lerModalidades',
    summary: 'List cardio and sport modalities',
    tags: ['Registro de exercício'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Modalities from the cardio and sports catalogs. The default intensity factor is 1.',
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/Modalidade'),
            ),
        ),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class LerModalidades
{
    public function __invoke(Request $request): JsonResponse
    {
        $lista = array_map(function (array $modalidade): array {
            return [
                'code' => $modalidade['code'],
                'name' => $modalidade['name'],
                'catalog' => $modalidade['catalog'],
                'intensities' => Catalogo::intensidades($modalidade),
            ];
        }, [
            ...Catalogo::modalidades('cardio'),
            ...Catalogo::modalidades('sports'),
        ]);

        return response()->json($lista);
    }
}
