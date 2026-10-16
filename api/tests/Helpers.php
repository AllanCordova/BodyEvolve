<?php

use App\Models\User;
use Illuminate\Testing\TestResponse;

function comoCliente(): void
{
    test()->withCredentials();
    test()->withHeader('Origin', 'http://localhost:5173');
    test()->withHeader('Referer', 'http://localhost:5173/');
    test()->withHeader('Accept', 'application/json');
}

function guardarCookies(TestResponse $resposta): TestResponse
{
    foreach ($resposta->headers->getCookies() as $cookie) {
        test()->withUnencryptedCookie($cookie->getName(), $cookie->getValue());
    }

    test()->soltarUsuarioDaSessao();

    return $resposta;
}

function entrarCom(string $email, string $password): void
{
    guardarCookies(test()->postJson('/api/acesso/sessao', [
        'email' => $email,
        'password' => $password,
    ]))->assertOk();
}

function usuarioComSenha(string $name, string $email, string $password = 'senha-segura'): User
{
    return User::factory()->create([
        'name' => $name,
        'email' => $email,
        'password' => $password,
    ]);
}

/**
 * @param  array<string, mixed>  $dados
 * @return array{age_years: int, sex: string, height_cm: int, weight_kg: int|float}
 */
function medidasDoCorpo(array $dados = []): array
{
    return [
        'age_years' => 30,
        'sex' => 'masculino',
        'height_cm' => 180,
        'weight_kg' => 80,
        ...$dados,
    ];
}
