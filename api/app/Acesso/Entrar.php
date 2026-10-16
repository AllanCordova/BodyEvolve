<?php

namespace App\Acesso;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/api/acesso/sessao',
    operationId: 'entrar',
    summary: 'Sign in with email and password',
    tags: ['Acesso'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/Credenciais'),
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Session started',
            content: new OA\JsonContent(ref: '#/components/schemas/Sessao'),
        ),
        new OA\Response(response: 422, description: 'Credentials do not match'),
    ],
)]
class Entrar
{
    public function __invoke(Request $request): JsonResponse
    {
        $dados = Credenciais::paraEntrada($request);

        if (! Auth::guard('web')->attempt($dados)) {
            throw ValidationException::withMessages([
                'email' => ['E-mail ou senha não conferem.'],
            ]);
        }

        $request->session()->regenerate();

        /** @var User $usuario */
        $usuario = Auth::guard('web')->user();

        return response()->json(Conta::resposta($usuario));
    }
}
