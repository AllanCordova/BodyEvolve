<?php

namespace App\RegistroDeExercicio;

use App\Algoritmo\GastoDaSessao;
use App\Perfil\Corpo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/api/treino/sessoes',
    operationId: 'guardarSessao',
    summary: 'Log a cardio or sport session for the current account',
    tags: ['Registro de exercício'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/SessaoNova'),
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Session saved. Net kcal is (base - 1) × weight × minutes / 60 × intensity factor.',
            content: new OA\JsonContent(ref: '#/components/schemas/Sessao'),
        ),
        new OA\Response(response: 401, description: 'No session'),
        new OA\Response(response: 422, description: 'Unknown modality, unknown intensity, or missing weight'),
    ],
)]
class GuardarSessao
{
    public function __invoke(Request $request): JsonResponse
    {
        $dados = Pedido::daSessao($request);
        $usuario = $request->user();
        $peso = Corpo::resposta($usuario)['weight_kg'] ?? null;

        if ($peso === null || $peso <= 0) {
            throw ValidationException::withMessages([
                'modality_code' => 'Informe o peso antes de lançar a sessão.',
            ]);
        }

        $modalidade = Catalogo::modalidade($dados['modality_code']);
        $fator = Catalogo::fator($dados['modality_code'], $dados['intensity_code']);
        $liquidas = GastoDaSessao::liquidas($modalidade['base'], $peso, $dados['duration_min'], $fator);

        $sessao = Sessao::query()->create([
            'user_id' => $usuario->id,
            'modality_code' => $dados['modality_code'],
            'trained_on' => $dados['trained_on'],
            'duration_min' => $dados['duration_min'],
            'intensity_factor' => $fator,
            'already_in_lifestyle' => $dados['already_in_lifestyle'],
            'net_kcal' => $liquidas,
        ]);

        return response()->json(self::resposta($sessao, $modalidade['name'], $dados['intensity_code']), 201);
    }

    /**
     * @return array{id: string, modality_code: string, modality_name: string, duration_min: int, intensity_code: string, intensity_factor: float, already_in_lifestyle: bool, trained_on: string, net_kcal: float}
     */
    public static function resposta(Sessao $sessao, string $nome, string $intensidade): array
    {
        return [
            'id' => $sessao->id,
            'modality_code' => $sessao->modality_code,
            'modality_name' => $nome,
            'duration_min' => $sessao->duration_min,
            'intensity_code' => $intensidade,
            'intensity_factor' => $sessao->intensity_factor,
            'already_in_lifestyle' => $sessao->already_in_lifestyle,
            'trained_on' => $sessao->trained_on->toDateString(),
            'net_kcal' => $sessao->net_kcal,
        ];
    }
}
