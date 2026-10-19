<?php

use App\Algoritmo\GastoEmRepouso;
use App\Algoritmo\Manutencao;
use Carbon\Carbon;

beforeEach(function () {
    comoCliente();
});

afterEach(function () {
    Carbon::setTestNow();
});

it('soma só as sessões que ainda não estão na rotina', function () {
    expect(Manutencao::kcal(1700, 'sedentaria', []))->toBe(2601);

    expect(Manutencao::kcal(1700, 'sedentaria', [
        ['net_kcal' => 228, 'already_in_lifestyle' => false],
        ['net_kcal' => 180, 'already_in_lifestyle' => true],
    ]))->toBe(2829);

    expect(Manutencao::kcal(1700, 'sedentaria', [
        ['net_kcal' => 180, 'already_in_lifestyle' => true],
    ]))->toBe(2601);
});

it('não calcula manutenção sem basal ou sem rotina', function () {
    expect(Manutencao::kcal(null, 'sedentaria'))->toBeNull();
    expect(Manutencao::kcal(1700, null))->toBeNull();
    expect(Manutencao::kcal(1700, 'treino'))->toBeNull();
});

it('mostra a manutenção do dia sem sessão como basal vezes o peso da rotina', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();
    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'sedentaria'])->assertOk();

    $basal = GastoEmRepouso::kcal(30, 'masculino', 180, 80);

    test()->getJson('/api/manutencao')->assertExactJson([
        'basal_kcal' => $basal,
        'lifestyle' => 'sedentaria',
        'maintenance_kcal' => 2723,
    ]);
});

it('usa o sexo e o peso novo no basal da manutenção', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');
    Carbon::setTestNow('2026-10-16 10:00:00');
    test()->putJson('/api/perfil', medidasDoCorpo(['sex' => 'feminino', 'weight_kg' => 80]))->assertOk();
    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'sedentaria'])->assertOk();

    test()->getJson('/api/manutencao')->assertExactJson([
        'basal_kcal' => 1614,
        'lifestyle' => 'sedentaria',
        'maintenance_kcal' => 2469,
    ]);

    Carbon::setTestNow('2026-10-16 10:00:01');
    test()->putJson('/api/perfil', medidasDoCorpo(['sex' => 'feminino', 'weight_kg' => 70]))->assertOk();

    test()->getJson('/api/manutencao')->assertExactJson([
        'basal_kcal' => 1514,
        'lifestyle' => 'sedentaria',
        'maintenance_kcal' => 2316,
    ]);
});

it('omite a manutenção quando falta o corpo ou a rotina', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');

    test()->getJson('/api/manutencao')->assertExactJson([
        'basal_kcal' => null,
        'lifestyle' => null,
    ]);

    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();

    test()->getJson('/api/manutencao')->assertExactJson([
        'basal_kcal' => 1780,
        'lifestyle' => null,
    ]);
});

it('não lê a manutenção sem sessão e não mostra a de outra conta', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    usuarioComSenha('Bia', 'bia@example.com', 'outra-senha');

    test()->getJson('/api/manutencao')->assertUnauthorized();

    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();
    test()->putJson('/api/perfil/rotina', ['lifestyle' => 'vigorosa'])->assertOk();

    entrarCom('bia@example.com', 'outra-senha');
    test()->getJson('/api/manutencao')->assertExactJson([
        'basal_kcal' => null,
        'lifestyle' => null,
    ]);

    entrarCom('ana@example.com', 'senha-segura');
    test()->getJson('/api/manutencao')->assertExactJson([
        'basal_kcal' => 1780,
        'lifestyle' => 'vigorosa',
        'maintenance_kcal' => 4005,
    ]);
});
