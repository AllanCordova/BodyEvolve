<?php

use App\Perfil\Perfil;
use App\Perfil\RegistroDePeso;

beforeEach(function () {
    comoCliente();
});

it('recusa sexo fora de masculino e feminino e recusa peso ou altura ausente', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    test()->putJson('/api/perfil', medidasDoCorpo(['sex' => 'outro']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('sex');

    $semPeso = medidasDoCorpo();
    unset($semPeso['weight_kg']);
    test()->putJson('/api/perfil', $semPeso)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('weight_kg');

    $semAltura = medidasDoCorpo();
    unset($semAltura['height_cm']);
    test()->putJson('/api/perfil', $semAltura)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('height_cm');

    expect(Perfil::query()->count())->toBe(0);
    expect(RegistroDePeso::query()->count())->toBe(0);
});

it('grava idade, sexo e altura no perfil e o peso em quilos', function () {
    $ana = usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    test()->putJson('/api/perfil', medidasDoCorpo())
        ->assertOk()
        ->assertExactJson([
            'age_years' => 30,
            'sex' => 'masculino',
            'height_cm' => 180,
            'weight_kg' => 80,
            'gasto_repouso_kcal' => 1780,
        ]);

    $perfil = Perfil::query()->find($ana->id);

    expect($perfil)->not->toBeNull();
    expect($perfil->age_years)->toBe(30);
    expect($perfil->sex)->toBe('masculino');
    expect($perfil->height_cm)->toBe(180);

    $peso = RegistroDePeso::query()->where('user_id', $ana->id)->first();

    expect($peso)->not->toBeNull();
    expect($peso->weight_kg)->toBe(80.0);
});

it('não grava nem lê o corpo sem sessão', function () {
    test()->putJson('/api/perfil', medidasDoCorpo())->assertUnauthorized();
    test()->getJson('/api/perfil')->assertUnauthorized();
});

it('não mostra o corpo de outra conta', function () {
    $ana = usuarioComSenha('Ana', 'ana@example.com');
    usuarioComSenha('Bia', 'bia@example.com', 'outra-senha');

    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();

    entrarCom('bia@example.com', 'outra-senha');
    test()->getJson('/api/perfil')->assertExactJson([
        'age_years' => null,
        'sex' => null,
        'height_cm' => null,
        'weight_kg' => null,
    ]);

    expect(Perfil::query()->where('user_id', $ana->id)->exists())->toBeTrue();
});
