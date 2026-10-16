<?php

namespace App\Perfil;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Escolha
{
    public static function doPedido(Request $request): string
    {
        if (array_keys($request->all()) !== ['lifestyle'] || is_array($request->input('lifestyle'))) {
            throw ValidationException::withMessages([
                'lifestyle' => 'Escolha uma rotina só.',
            ]);
        }

        /** @var array{lifestyle: string} $dados */
        $dados = $request->validate([
            'lifestyle' => ['required', 'string', 'in:sedentaria,moderada,vigorosa'],
        ], [
            'lifestyle.required' => 'Escolha uma rotina.',
            'lifestyle.string' => 'Escolha uma rotina só.',
            'lifestyle.in' => 'A rotina é sedentária, moderada ou vigorosa.',
        ]);

        return $dados['lifestyle'];
    }
}
