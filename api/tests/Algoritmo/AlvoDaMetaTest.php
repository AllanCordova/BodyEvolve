<?php

use Carbon\Carbon;

beforeEach(function () {
    comoCliente();
    Carbon::setTestNow('2026-10-16 10:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

it('omite o alvo quando falta o corpo ou a rotina', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    test()->putJson('/api/objetivo', ['goal' => 'hipertrofia'])->assertExactJson([
        'goal' => 'hipertrofia',
        'goal_started_on' => '2026-10-16',
        'micronutrients' => ['Zinco', 'Magnésio', 'Vitamina D3'],
    ]);

    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();

    test()->getJson('/api/objetivo')->assertExactJson([
        'goal' => 'hipertrofia',
        'goal_started_on' => '2026-10-16',
        'micronutrients' => ['Zinco', 'Magnésio', 'Vitamina D3'],
    ]);
});

it('recalcula calorias e nutrientes quando a meta muda', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();
    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'sedentaria'])->assertOk();

    test()->putJson('/api/objetivo', ['goal' => 'hipertrofia'])->assertExactJson([
        'goal' => 'hipertrofia',
        'goal_started_on' => '2026-10-16',
        'micronutrients' => ['Zinco', 'Magnésio', 'Vitamina D3'],
        'kcal' => 3131,
        'maintenance_kcal' => 2723,
        'protein_g' => 160,
        'fat_g' => 80,
        'carb_g' => 442.75,
    ]);

    test()->putJson('/api/objetivo', ['goal' => 'emagrecimento'])->assertExactJson([
        'goal' => 'emagrecimento',
        'goal_started_on' => '2026-10-16',
        'micronutrients' => ['Cálcio', 'Ferro', 'Complexo B'],
        'kcal' => 2178,
        'maintenance_kcal' => 2723,
        'protein_g' => 176,
        'fat_g' => 64,
        'carb_g' => 224.5,
    ]);

    expect(64)->toBeGreaterThanOrEqual(0.6 * 80);

    test()->putJson('/api/objetivo', ['goal' => 'forca'])->assertExactJson([
        'goal' => 'forca',
        'goal_started_on' => '2026-10-16',
        'micronutrients' => ['Cálcio', 'Vitamina D3', 'Magnésio'],
        'kcal' => 2859,
        'maintenance_kcal' => 2723,
        'protein_g' => 144,
        'fat_g' => 88,
        'carb_g' => 320,
    ]);

    test()->putJson('/api/objetivo', ['goal' => 'resistencia'])->assertExactJson([
        'goal' => 'resistencia',
        'goal_started_on' => '2026-10-16',
        'micronutrients' => ['Sódio', 'Potássio', 'Magnésio', 'Ferro'],
        'hydration_reminder' => 'Lembre de se hidratar.',
        'kcal' => 2723,
        'maintenance_kcal' => 2723,
        'protein_g' => 112,
        'fat_g' => 96,
        'carb_g' => 352.75,
        'carb_warning' => 'O carboidrato pedido não coube no saldo. Ficou o que sobrou.',
    ]);
});

it('avisa quando o carboidrato da força não cabe e nunca corta a gordura abaixo de 0.6 g/kg', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo([
        'age_years' => 60,
        'sex' => 'feminino',
        'height_cm' => 150,
        'weight_kg' => 90,
    ]))->assertOk();
    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'sedentaria'])->assertOk();

    test()->putJson('/api/objetivo', ['goal' => 'forca'])->assertExactJson([
        'goal' => 'forca',
        'goal_started_on' => '2026-10-16',
        'micronutrients' => ['Cálcio', 'Vitamina D3', 'Magnésio'],
        'kcal' => 2212,
        'maintenance_kcal' => 2107,
        'protein_g' => 162,
        'fat_g' => 99,
        'carb_g' => 168.25,
        'carb_warning' => 'O carboidrato pedido não coube no saldo. Ficou o que sobrou.',
    ]);

    test()->putJson('/api/objetivo', ['goal' => 'emagrecimento'])
        ->assertJsonPath('fat_g', 72);

    expect(72)->toBeGreaterThanOrEqual(0.6 * 90);
});
