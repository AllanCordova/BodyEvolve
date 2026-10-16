<?php

namespace App\Acesso;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

#[OA\Delete(
    path: '/api/acesso/sessao',
    operationId: 'sair',
    summary: 'Sign out and end the session',
    tags: ['Acesso'],
    responses: [
        new OA\Response(response: 204, description: 'Session ended'),
        new OA\Response(response: 401, description: 'No session'),
    ],
)]
class Sair
{
    public function __invoke(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
