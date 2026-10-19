<?php

use App\Objetivo\Meta;
use Carbon\Carbon;

beforeEach(function () {
    comoCliente();
});

afterEach(function () {
    Carbon::setTestNow();
});

it('grava uma das quatro metas e troca a data só quando a meta muda', function () {
    $ana = usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    Carbon::setTestNow('2026-10-16 10:00:00');
    test()->putJson('/api/objetivo', ['goal' => 'hipertrofia'])->assertExactJson([
        'goal' => 'hipertrofia',
        'goal_started_on' => '2026-10-16',
        'micronutrients' => ['Zinco', 'Magnésio', 'Vitamina D3'],
    ]);

    Carbon::setTestNow('2026-10-17 10:00:00');
    test()->putJson('/api/objetivo', ['goal' => 'hipertrofia'])->assertExactJson([
        'goal' => 'hipertrofia',
        'goal_started_on' => '2026-10-16',
        'micronutrients' => ['Zinco', 'Magnésio', 'Vitamina D3'],
    ]);

    test()->putJson('/api/objetivo', ['goal' => 'forca'])->assertExactJson([
        'goal' => 'forca',
        'goal_started_on' => '2026-10-17',
        'micronutrients' => ['Cálcio', 'Vitamina D3', 'Magnésio'],
    ]);

    expect(Meta::query()->count())->toBe(1);
    expect(Meta::query()->find($ana->id)->goal)->toBe('forca');
});

it('recusa meta desconhecida e recusa combinação de duas', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    test()->putJson('/api/objetivo', ['goal' => 'outra'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('goal');

    test()->putJson('/api/objetivo', ['goal' => ['hipertrofia', 'emagrecimento']])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('goal');

    test()->putJson('/api/objetivo', [
        'goal' => 'hipertrofia',
        'outra' => 'emagrecimento',
    ])->assertUnprocessable()->assertJsonValidationErrors('goal');

    expect(Meta::query()->count())->toBe(0);
});

it('não grava nem lê a meta sem sessão', function () {
    test()->putJson('/api/objetivo', ['goal' => 'hipertrofia'])->assertUnauthorized();
    test()->getJson('/api/objetivo')->assertUnauthorized();
});

it('não mostra a meta de outra conta', function () {
    $ana = usuarioComSenha('Ana', 'ana@example.com');
    usuarioComSenha('Bia', 'bia@example.com', 'outra-senha');

    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/objetivo', ['goal' => 'resistencia'])->assertOk();

    entrarCom('bia@example.com', 'outra-senha');
    test()->getJson('/api/objetivo')->assertExactJson([
        'goal' => null,
    ]);

    expect(Meta::query()->where('user_id', $ana->id)->value('goal'))->toBe('resistencia');
});
