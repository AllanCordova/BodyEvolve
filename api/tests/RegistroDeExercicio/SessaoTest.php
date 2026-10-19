<?php

use App\Algoritmo\GastoDaSessao;
use App\RegistroDeExercicio\Sessao;
use Carbon\Carbon;

beforeEach(function () {
    comoCliente();
    Carbon::setTestNow('2026-10-19 10:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

it('calcula o líquido com a base, o peso, os minutos e o fator', function () {
    expect(GastoDaSessao::liquidas(4.8, 80, 45, 1))->toBe(228.0);
    expect(GastoDaSessao::liquidas(4.8, 80, 45, 1.15))->toBe(262.2);
});

it('grava cardio ou esporte e soma na manutenção só o que não está na rotina', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();
    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'sedentaria'])->assertOk();

    test()->postJson('/api/treino/sessoes', [
        'modality_code' => 'caminhada-rapida',
        'duration_min' => 45,
        'intensity_code' => 'muito-rapida',
        'already_in_lifestyle' => false,
    ])->assertCreated()->assertJson([
        'modality_code' => 'caminhada-rapida',
        'modality_name' => 'Caminhada rápida',
        'duration_min' => 45,
        'intensity_code' => 'muito-rapida',
        'intensity_factor' => 1.15,
        'already_in_lifestyle' => false,
        'trained_on' => '2026-10-19',
        'net_kcal' => 262.2,
    ]);

    test()->postJson('/api/treino/sessoes', [
        'modality_code' => 'futebol',
        'duration_min' => 60,
        'intensity_code' => 'padrao',
        'already_in_lifestyle' => true,
    ])->assertCreated()->assertJsonPath('net_kcal', 480);

    test()->getJson('/api/manutencao')->assertJsonPath('maintenance_kcal', 2985);
    test()->getJson('/api/treino/sessoes')->assertJsonCount(2);

    expect(Sessao::query()->count())->toBe(2);
});

it('recusa modalidade fora do cardio e do esporte, intensidade inventada e sessão sem peso', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    test()->postJson('/api/treino/sessoes', [
        'modality_code' => 'caminhada-rapida',
        'duration_min' => 30,
        'intensity_code' => 'padrao',
        'already_in_lifestyle' => false,
    ])->assertUnprocessable()->assertJsonValidationErrors('modality_code');

    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();

    test()->postJson('/api/treino/sessoes', [
        'modality_code' => 'musculacao',
        'duration_min' => 60,
        'intensity_code' => 'padrao',
        'already_in_lifestyle' => false,
    ])->assertUnprocessable()->assertJsonValidationErrors('modality_code');

    test()->postJson('/api/treino/sessoes', [
        'modality_code' => 'corrida',
        'duration_min' => 30,
        'intensity_code' => 'outra',
        'already_in_lifestyle' => false,
    ])->assertUnprocessable()->assertJsonValidationErrors('intensity_code');

    expect(Sessao::query()->count())->toBe(0);
});

it('não grava nem lista sessão sem conta e não mostra a de outra pessoa', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    usuarioComSenha('Bia', 'bia@example.com', 'outra-senha');

    test()->postJson('/api/treino/sessoes', [
        'modality_code' => 'futsal',
        'duration_min' => 40,
        'intensity_code' => 'padrao',
        'already_in_lifestyle' => false,
    ])->assertUnauthorized();

    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();
    test()->postJson('/api/treino/sessoes', [
        'modality_code' => 'futsal',
        'duration_min' => 40,
        'intensity_code' => 'padrao',
        'already_in_lifestyle' => false,
    ])->assertCreated();

    entrarCom('bia@example.com', 'outra-senha');
    test()->getJson('/api/treino/sessoes')->assertExactJson([]);
});

it('lista cardio e esporte com a intensidade padrão no fator 1', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    $lista = test()->getJson('/api/treino/modalidades')->assertOk()->json();
    $caminhada = collect($lista)->firstWhere('code', 'caminhada-rapida');
    $halterofilismo = collect($lista)->firstWhere('code', 'halterofilismo');

    expect($caminhada['catalog'])->toBe('cardio');
    expect($caminhada['intensities'][0])->toBe(['code' => 'padrao', 'name' => 'Padrão', 'factor' => 1]);
    expect($caminhada['intensities'][2]['factor'])->toBe(1.15);
    expect($halterofilismo['catalog'])->toBe('sports');
    expect(collect($lista)->contains(fn (array $item): bool => $item['catalog'] === 'strong'))->toBeFalse();
});
