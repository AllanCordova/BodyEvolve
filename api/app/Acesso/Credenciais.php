<?php

namespace App\Acesso;

use Illuminate\Http\Request;

class Credenciais
{
    /**
     * @return array{name: string, email: string, password: string}
     */
    public static function paraContaNova(Request $request): array
    {
        self::normalizarEmail($request);
        $request->merge([
            'name' => trim((string) $request->input('name', '')),
        ]);

        /** @var array{name: string, email: string, password: string} $dados */
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ], self::mensagens());

        return $dados;
    }

    /**
     * @return array{email: string, password: string}
     */
    public static function paraEntrada(Request $request): array
    {
        self::normalizarEmail($request);

        /** @var array{email: string, password: string} $dados */
        $dados = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], self::mensagens());

        return $dados;
    }

    private static function normalizarEmail(Request $request): void
    {
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email', ''))),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private static function mensagens(): array
    {
        return [
            'name.required' => 'Informe o nome.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Já existe uma conta com esse e-mail.',
            'password.required' => 'Informe a senha.',
            'password.min' => 'A senha precisa ter pelo menos :min caracteres.',
        ];
    }
}
