<?php

namespace App\Perfil;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Put(
    path: '/api/perfil',
    operationId: 'guardarCorpo',
    summary: 'Save age, biological sex, height, and weight',
    tags: ['Perfil'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/CorpoNovo'),
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Body saved. Resting energy uses the latest weight.',
            content: new OA\JsonContent(ref: '#/components/schemas/Corpo'),
        ),
        new OA\Response(response: 401, description: 'No session'),
        new OA\Response(response: 422, description: 'Invalid body data'),
    ],
)]
class GuardarCorpo
{
    public function __invoke(Request $request): JsonResponse
    {
        $dados = Medidas::doPedido($request);
        $usuario = $request->user();

        Perfil::query()->updateOrCreate(
            ['user_id' => $usuario->id],
            [
                'age_years' => $dados['age_years'],
                'sex' => $dados['sex'],
                'height_cm' => $dados['height_cm'],
            ],
        );

        RegistroDePeso::query()->create([
            'user_id' => $usuario->id,
            'weight_kg' => $dados['weight_kg'],
            'recorded_on' => now()->toDateString(),
        ]);

        return response()->json(Corpo::resposta($usuario));
    }
}
