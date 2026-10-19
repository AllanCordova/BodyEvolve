<?php

use App\RegistroDeKcal\Comida;
use Carbon\Carbon;

beforeEach(function () {
    comoCliente();
    Carbon::setTestNow('2026-10-19 10:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

it('grava um registro do dia, inclusive a fibra, e substitui o mesmo dia', function () {
    $ana = usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    test()->putJson('/api/comida', [
        'kcal' => 2200,
        'protein_g' => 150,
        'fat_g' => 70,
        'carb_g' => 200,
        'fiber_g' => 28,
    ])->assertExactJson([
        'intake_date' => '2026-10-19',
        'kcal' => 2200,
        'protein_g' => 150,
        'fat_g' => 70,
        'carb_g' => 200,
        'fiber_g' => 28,
    ]);

    test()->putJson('/api/comida', [
        'kcal' => 2100,
        'protein_g' => 140,
        'fat_g' => 65,
        'carb_g' => 190,
        'fiber_g' => 30,
    ])->assertJsonPath('kcal', 2100);

    expect(Comida::query()->where('user_id', $ana->id)->count())->toBe(1);
    expect(Comida::query()->first()->fiber_g)->toEqual(30);
});

it('não grava comida de cada alimento e não mostra a de outra conta', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    usuarioComSenha('Bia', 'bia@example.com', 'outra-senha');

    test()->putJson('/api/comida', [
        'kcal' => 1,
        'protein_g' => 1,
        'fat_g' => 1,
        'carb_g' => 1,
        'fiber_g' => 1,
    ])->assertUnauthorized();

    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/comida', [
        'kcal' => 1800,
        'protein_g' => 120,
        'fat_g' => 50,
        'carb_g' => 160,
        'fiber_g' => 22,
        'foods' => [['name' => 'arroz']],
    ])->assertJsonPath('fiber_g', 22);

    expect(Comida::query()->count())->toBe(1);

    entrarCom('bia@example.com', 'outra-senha');
    test()->getJson('/api/comida')->assertExactJson([
        'intake_date' => '2026-10-19',
        'kcal' => null,
        'protein_g' => null,
        'fat_g' => null,
        'carb_g' => null,
        'fiber_g' => null,
    ]);
});
