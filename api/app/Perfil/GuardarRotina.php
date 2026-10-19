<?php

namespace App\Perfil;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Put(
    path: '/api/perfil/rotina',
    operationId: 'guardarRotina',
    summary: 'Save how active the day is for the current account',
    tags: ['Perfil'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/RotinaNova'),
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Routine saved. Expenditure is basal times the routine weight, and is present only when age, sex, weight, and height exist.',
            content: new OA\JsonContent(ref: '#/components/schemas/Rotina'),
        ),
        new OA\Response(response: 401, description: 'No session'),
        new OA\Response(response: 422, description: 'Missing routine, unknown routine, or fields besides the routine'),
    ],
)]
class GuardarRotina
{
    public function __invoke(Request $request): JsonResponse
    {
        $escolha = Escolha::doPedido($request);
        $usuario = $request->user();
        $perfil = Perfil::query()->find($usuario->id);

        if ($perfil === null) {
            Perfil::query()->create([
                'user_id' => $usuario->id,
                'lifestyle' => $escolha,
            ]);
        } else {
            $perfil->lifestyle = $escolha;
            $perfil->save();
        }

        return response()->json(Rotina::resposta($usuario));
    }
}
