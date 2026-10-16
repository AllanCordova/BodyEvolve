<?php

namespace App\Objetivo;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Escolha
{
    public static function doPedido(Request $request): string
    {
        if (array_keys($request->all()) !== ['goal'] || is_array($request->input('goal'))) {
            throw ValidationException::withMessages([
                'goal' => 'Escolha uma meta só.',
            ]);
        }

        /** @var array{goal: string} $dados */
        $dados = $request->validate([
            'goal' => ['required', 'string', 'in:hipertrofia,emagrecimento,forca,resistencia'],
        ], [
            'goal.required' => 'Escolha uma meta.',
            'goal.string' => 'Escolha uma meta só.',
            'goal.in' => 'A meta é hipertrofia, emagrecimento, força ou resistência.',
        ]);

        return $dados['goal'];
    }
}
