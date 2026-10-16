<?php

use App\Models\User;

beforeEach(function () {
    comoCliente();
});

it('entra com o e-mail e a senha da conta', function () {
    User::factory()->create([
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'password' => 'senha-segura',
    ]);

    $resposta = guardarCookies(test()->postJson('/api/acesso/sessao', [
        'email' => 'Ana@Example.com',
        'password' => 'senha-segura',
    ]));

    $conta = [
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'icon_url' => null,
    ];

    $resposta->assertOk();
    $resposta->assertExactJson($conta);

    test()->getJson('/api/acesso/sessao')
        ->assertOk()
        ->assertExactJson($conta);
});

it('recusa senha que não confere e não abre sessão', function () {
    User::factory()->create([
        'email' => 'ana@example.com',
        'password' => 'senha-segura',
    ]);

    test()->postJson('/api/acesso/sessao', [
        'email' => 'ana@example.com',
        'password' => 'senha-errada',
    ])->assertUnprocessable()->assertJsonValidationErrors('email');

    test()->getJson('/api/acesso/sessao')->assertUnauthorized();
});
