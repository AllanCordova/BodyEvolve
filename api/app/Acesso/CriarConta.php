<?php

namespace App\Acesso;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/api/acesso/conta',
    operationId: 'criarConta',
    summary: 'Create an account with name, email and password',
    tags: ['Acesso'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/ContaNova'),
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Account created and session started',
            content: new OA\JsonContent(ref: '#/components/schemas/Sessao'),
        ),
        new OA\Response(response: 422, description: 'Invalid account data'),
    ],
)]
class CriarConta
{
    public function __invoke(Request $request): JsonResponse
    {
        $dados = Credenciais::paraContaNova($request);

        $usuario = User::query()->create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'password' => $dados['password'],
        ]);

        Auth::guard('web')->login($usuario);
        $request->session()->regenerate();

        return response()->json(Conta::resposta($usuario), 201);
    }
}
