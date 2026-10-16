<?php

use Carbon\Carbon;

beforeEach(function () {
    comoCliente();
    Carbon::setTestNow('2026-10-16 10:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

it('omite o alvo quando falta algum dos quatro dados do corpo', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    test()->putJson('/api/objetivo', ['goal' => 'hipertrofia'])->assertExactJson([
        'goal' => 'hipertrofia',
        'goal_started_on' => '2026-10-16',
    ]);
});

it('recalcula calorias e nutrientes quando a meta muda', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();

    test()->putJson('/api/objetivo', ['goal' => 'hipertrofia'])->assertExactJson([
        'goal' => 'hipertrofia',
        'goal_started_on' => '2026-10-16',
        'kcal' => 2047,
        'protein_g' => 160,
        'fat_g' => 80,
        'carb_g' => 171.75,
    ]);

    test()->putJson('/api/objetivo', ['goal' => 'emagrecimento'])->assertExactJson([
        'goal' => 'emagrecimento',
        'goal_started_on' => '2026-10-16',
        'kcal' => 1424,
        'protein_g' => 176,
        'fat_g' => 64,
        'carb_g' => 36,
    ]);

    test()->putJson('/api/objetivo', ['goal' => 'forca'])->assertExactJson([
        'goal' => 'forca',
        'goal_started_on' => '2026-10-16',
        'kcal' => 1869,
        'protein_g' => 144,
        'fat_g' => 88,
        'carb_g' => 125.25,
    ]);

    test()->putJson('/api/objetivo', ['goal' => 'resistencia'])->assertExactJson([
        'goal' => 'resistencia',
        'goal_started_on' => '2026-10-16',
        'kcal' => 1780,
        'protein_g' => 112,
        'fat_g' => 96,
        'carb_g' => 117,
    ]);
});
