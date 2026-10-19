<?php

namespace App\RegistroDeExercicio;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/treino/sessoes',
    operationId: 'lerSessoes',
    summary: 'List training sessions for one day of the current account',
    tags: ['Registro de exercício'],
    parameters: [
        new OA\Parameter(name: 'trained_on', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Sessions of the given day, or of today.',
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/Sessao'),
            ),
        ),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class LerSessoes
{
    public function __invoke(Request $request): JsonResponse
    {
        $dia = $request->validate([
            'trained_on' => ['sometimes', 'date'],
        ])['trained_on'] ?? now()->toDateString();

        $sessoes = Sessao::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('trained_on', $dia)
            ->orderBy('created_at')
            ->get()
            ->map(function (Sessao $sessao): array {
                $modalidade = Catalogo::modalidade($sessao->modality_code);

                return GuardarSessao::resposta(
                    $sessao,
                    $modalidade['name'] ?? $sessao->modality_code,
                    self::intensidade($sessao),
                );
            })
            ->all();

        return response()->json($sessoes);
    }

    private static function intensidade(Sessao $sessao): string
    {
        $modalidade = Catalogo::modalidade($sessao->modality_code);

        if ($modalidade === null || abs($sessao->intensity_factor - 1) < 0.001) {
            return 'padrao';
        }

        foreach ($modalidade['anchors'] as $ancora) {
            if (abs($ancora['factor'] - $sessao->intensity_factor) < 0.001) {
                return $ancora['code'];
            }
        }

        return 'padrao';
    }
}
