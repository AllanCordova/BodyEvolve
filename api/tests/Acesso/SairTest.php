<?php

use App\Models\User;

beforeEach(function () {
    comoCliente();
});

it('encerra o acesso ao sair', function () {
    User::factory()->create([
        'email' => 'ana@example.com',
        'password' => 'senha-segura',
    ]);

    guardarCookies(test()->postJson('/api/acesso/sessao', [
        'email' => 'ana@example.com',
        'password' => 'senha-segura',
    ]))->assertOk();

    guardarCookies(test()->deleteJson('/api/acesso/sessao'))->assertNoContent();

    test()->getJson('/api/acesso/sessao')->assertUnauthorized();
    test()->getJson('/api/perfil')->assertUnauthorized();
    test()->getJson('/api/treino')->assertUnauthorized();
    test()->getJson('/api/comida')->assertUnauthorized();
    test()->getJson('/api/alertas')->assertUnauthorized();
});
