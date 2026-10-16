<?php

use App\Perfil\Perfil;
use App\Perfil\RegistroDePeso;

beforeEach(function () {
    comoCliente();
});

it('grava a rotina e troca o gasto quando a escolha muda', function () {
    $ana = usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();

    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'sedentaria'])->assertExactJson([
        'lifestyle' => 'sedentaria',
        'gasto_da_rotina_kcal' => 2723,
    ]);

    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'moderada'])->assertExactJson([
        'lifestyle' => 'moderada',
        'gasto_da_rotina_kcal' => 3133,
    ]);

    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'vigorosa'])->assertExactJson([
        'lifestyle' => 'vigorosa',
        'gasto_da_rotina_kcal' => 4005,
    ]);

    test()->getJson('/api/perfil/rotina')->assertExactJson([
        'lifestyle' => 'vigorosa',
        'gasto_da_rotina_kcal' => 4005,
    ]);

    expect(Perfil::query()->count())->toBe(1);
    expect(Perfil::query()->find($ana->id)->lifestyle)->toBe('vigorosa');
    expect(RegistroDePeso::query()->count())->toBe(1);
});

it('guarda a rotina sem gasto quando o corpo ainda não existe', function () {
    $ana = usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'moderada'])->assertExactJson([
        'lifestyle' => 'moderada',
    ]);

    test()->getJson('/api/perfil')->assertExactJson([
        'age_years' => null,
        'sex' => null,
        'height_cm' => null,
        'weight_kg' => null,
    ]);

    expect(Perfil::query()->find($ana->id)->lifestyle)->toBe('moderada');
    expect(RegistroDePeso::query()->count())->toBe(0);

    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();
    test()->getJson('/api/perfil/rotina')->assertExactJson([
        'lifestyle' => 'moderada',
        'gasto_da_rotina_kcal' => 3133,
    ]);
});

it('recusa rotina desconhecida, combinação e campos de sessão', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    test()->putJson('/api/perfil/rotina', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('lifestyle');

    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'treino'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('lifestyle');

    test()->putJson('/api/perfil/rotina', ['lifestyle' => ['sedentaria', 'vigorosa']])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('lifestyle');

    test()->putJson('/api/perfil/rotina', [
        'lifestyle' => 'moderada',
        'modality' => 'caminhada',
        'duration_min' => 45,
        'intensity_factor' => 1,
    ])->assertUnprocessable()->assertJsonValidationErrors('lifestyle');

    expect(Perfil::query()->count())->toBe(0);
    expect(RegistroDePeso::query()->count())->toBe(0);
});

it('não grava nem lê a rotina sem sessão', function () {
    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'sedentaria'])->assertUnauthorized();
    test()->getJson('/api/perfil/rotina')->assertUnauthorized();
});

it('não mostra a rotina de outra conta', function () {
    $ana = usuarioComSenha('Ana', 'ana@example.com');
    usuarioComSenha('Bia', 'bia@example.com', 'outra-senha');

    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'vigorosa'])->assertOk();

    entrarCom('bia@example.com', 'outra-senha');
    test()->getJson('/api/perfil/rotina')->assertExactJson([
        'lifestyle' => null,
    ]);

    expect(Perfil::query()->where('user_id', $ana->id)->value('lifestyle'))->toBe('vigorosa');
});
