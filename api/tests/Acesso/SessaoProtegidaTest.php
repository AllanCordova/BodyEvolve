<?php

use App\Models\User;

beforeEach(function () {
    comoCliente();
});

it('não abre perfil, treino, comida nem alertas sem sessão', function () {
    test()->getJson('/api/acesso/sessao')->assertUnauthorized();
    test()->getJson('/api/perfil')->assertUnauthorized();
    test()->getJson('/api/treino')->assertUnauthorized();
    test()->getJson('/api/comida')->assertUnauthorized();
    test()->getJson('/api/alertas')->assertUnauthorized();
});

it('devolve a conta da sessão e deixa as áreas vazias', function () {
    User::factory()->create([
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'password' => 'senha-segura',
    ]);
    User::factory()->create([
        'name' => 'Bia',
        'email' => 'bia@example.com',
        'password' => 'outra-senha',
    ]);

    guardarCookies(test()->postJson('/api/acesso/sessao', [
        'email' => 'ana@example.com',
        'password' => 'senha-segura',
    ]))->assertOk();

    test()->getJson('/api/acesso/sessao')->assertExactJson([
        'name' => 'Ana',
        'email' => 'ana@example.com',
        'icon_url' => null,
    ]);
    test()->getJson('/api/perfil')->assertExactJson([
        'age_years' => null,
        'sex' => null,
        'height_cm' => null,
        'weight_kg' => null,
    ]);
    test()->getJson('/api/treino')->assertNoContent();
    test()->getJson('/api/comida')->assertExactJson([
        'intake_date' => now()->toDateString(),
        'kcal' => null,
        'protein_g' => null,
        'fat_g' => null,
        'carb_g' => null,
        'fiber_g' => null,
    ]);
    test()->getJson('/api/alertas')->assertNoContent();

    guardarCookies(test()->deleteJson('/api/acesso/sessao'))->assertNoContent();

    guardarCookies(test()->postJson('/api/acesso/sessao', [
        'email' => 'bia@example.com',
        'password' => 'outra-senha',
    ]))->assertOk();

    test()->getJson('/api/acesso/sessao')->assertExactJson([
        'name' => 'Bia',
        'email' => 'bia@example.com',
        'icon_url' => null,
    ]);
});
