<?php

namespace App\RegistroDeKcal;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/comida',
    operationId: 'lerComida',
    summary: 'Read the day\'s intake for the current account',
    tags: ['Registro de kcal'],
    parameters: [
        new OA\Parameter(name: 'intake_date', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
    ],
    responses: [
        new OA\Response(response: 200, description: 'The day\'s intake, or an empty day.', content: new OA\JsonContent(ref: '#/components/schemas/Comida')),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class LerComida
{
    public function __invoke(Request $request): JsonResponse
    {
        $dia = $request->validate([
            'intake_date' => ['sometimes', 'date'],
        ])['intake_date'] ?? now()->toDateString();

        return response()->json(self::doDia($request->user()->id, $dia));
    }

    /**
     * @return array{intake_date: string, kcal: int|null, protein_g: float|null, fat_g: float|null, carb_g: float|null, fiber_g: float|null}
     */
    public static function doDia(string $usuarioId, string $dia): array
    {
        $comida = Comida::query()
            ->where('user_id', $usuarioId)
            ->whereDate('intake_date', $dia)
            ->first();

        if ($comida === null) {
            return [
                'intake_date' => $dia,
                'kcal' => null,
                'protein_g' => null,
                'fat_g' => null,
                'carb_g' => null,
                'fiber_g' => null,
            ];
        }

        return [
            'intake_date' => $comida->intake_date->toDateString(),
            'kcal' => $comida->kcal,
            'protein_g' => $comida->protein_g,
            'fat_g' => $comida->fat_g,
            'carb_g' => $comida->carb_g,
            'fiber_g' => $comida->fiber_g,
        ];
    }
}
