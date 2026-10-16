<?php

use App\Perfil\RegistroDePeso;
use Carbon\Carbon;

beforeEach(function () {
    comoCliente();
});

afterEach(function () {
    Carbon::setTestNow();
});

it('não calcula gasto, manutenção nem meta sem os quatro dados', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    test()->getJson('/api/perfil')->assertExactJson([
        'age_years' => null,
        'sex' => null,
        'height_cm' => null,
        'weight_kg' => null,
    ]);
});

it('calcula o gasto em repouso e deixa manutenção e meta de fora', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    usuarioComSenha('Bia', 'bia@example.com', 'outra-senha');

    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertExactJson([
        'age_years' => 30,
        'sex' => 'masculino',
        'height_cm' => 180,
        'weight_kg' => 80,
        'gasto_repouso_kcal' => 1780,
    ]);

    entrarCom('bia@example.com', 'outra-senha');
    test()->putJson('/api/perfil', medidasDoCorpo(['sex' => 'feminino']))->assertExactJson([
        'age_years' => 30,
        'sex' => 'feminino',
        'height_cm' => 180,
        'weight_kg' => 80,
        'gasto_repouso_kcal' => 1614,
    ]);
});

it('usa o peso novo no cálculo seguinte e guarda o peso anterior', function () {
    $ana = usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    Carbon::setTestNow('2026-10-16 10:00:00');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();

    Carbon::setTestNow('2026-10-16 10:00:01');
    test()->putJson('/api/perfil', medidasDoCorpo(['weight_kg' => 70]))->assertExactJson([
        'age_years' => 30,
        'sex' => 'masculino',
        'height_cm' => 180,
        'weight_kg' => 70,
        'gasto_repouso_kcal' => 1680,
    ]);

    $pesos = RegistroDePeso::query()
        ->where('user_id', $ana->id)
        ->orderBy('weight_kg')
        ->pluck('weight_kg')
        ->all();

    expect($pesos)->toEqual([70.0, 80.0]);
});
