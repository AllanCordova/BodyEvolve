<?php

namespace App\RegistroDeKcal;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Put(
    path: '/api/comida',
    operationId: 'guardarComida',
    summary: 'Save the day\'s intake for the current account',
    tags: ['Registro de kcal'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/ComidaNova'),
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'One record for the day. Fiber is stored and has no cutoff.',
            content: new OA\JsonContent(ref: '#/components/schemas/Comida'),
        ),
        new OA\Response(response: 401, description: 'No session'),
        new OA\Response(response: 422, description: 'Invalid intake'),
    ],
)]
class GuardarComida
{
    public function __invoke(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'intake_date' => ['sometimes', 'date'],
            'kcal' => ['required', 'integer', 'min:0'],
            'protein_g' => ['required', 'numeric', 'min:0'],
            'fat_g' => ['required', 'numeric', 'min:0'],
            'carb_g' => ['required', 'numeric', 'min:0'],
            'fiber_g' => ['required', 'numeric', 'min:0'],
        ], [
            'kcal.required' => 'Informe as calorias.',
            'protein_g.required' => 'Informe a proteína.',
            'fat_g.required' => 'Informe a gordura.',
            'carb_g.required' => 'Informe o carboidrato.',
            'fiber_g.required' => 'Informe a fibra.',
        ]);

        $usuario = $request->user();
        $dia = $dados['intake_date'] ?? now()->toDateString();

        Comida::query()->updateOrCreate(
            ['user_id' => $usuario->id, 'intake_date' => $dia],
            [
                'kcal' => $dados['kcal'],
                'protein_g' => $dados['protein_g'],
                'fat_g' => $dados['fat_g'],
                'carb_g' => $dados['carb_g'],
                'fiber_g' => $dados['fiber_g'],
            ],
        );

        return response()->json(LerComida::doDia($usuario->id, $dia));
    }
}
