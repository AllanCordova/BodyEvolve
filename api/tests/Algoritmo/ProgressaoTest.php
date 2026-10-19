<?php

use Carbon\Carbon;

beforeEach(function () {
    comoCliente();
    Carbon::setTestNow('2026-10-19 10:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

function lancarForca(string $exercicio, int $reps, float $carga, string $quando): void
{
    Carbon::setTestNow($quando);
    test()->postJson('/api/treino/sessoes', [
        'modality_code' => 'musculacao',
        'duration_min' => 40,
        'intensity_code' => 'padrao',
        'already_in_lifestyle' => false,
        'exercises' => [[
            'exercise_code' => $exercicio,
            'sets_done' => 3,
            'reps_done' => $reps,
            'load_kg' => $carga,
        ]],
    ])->assertCreated();
}

it('não alerta na primeira vez e marca progrediu, a caminho ou estagnado', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();
    test()->putJson('/api/objetivo', ['goal' => 'hipertrofia'])->assertOk();

    lancarForca('agachamento', 8, 100, '2026-10-19 10:00:00');
    test()->getJson('/api/treino/progressao')->assertJsonPath('0.state', null);

    lancarForca('agachamento', 8, 110, '2026-10-20 10:00:00');
    test()->getJson('/api/treino/progressao')->assertJsonPath('0.state', 'progrediu');

    lancarForca('supino', 8, 60, '2026-10-20 11:00:00');
    lancarForca('supino', 10, 60, '2026-10-21 11:00:00');
    $supino = collect(test()->getJson('/api/treino/progressao')->json())->firstWhere('exercise_code', 'supino');
    expect($supino['state'])->toBe('a_caminho');

    lancarForca('remada', 8, 50, '2026-10-21 12:00:00');
    lancarForca('remada', 8, 50, '2026-10-22 12:00:00');
    $remada = collect(test()->getJson('/api/treino/progressao')->json())->firstWhere('exercise_code', 'remada');
    expect($remada['state'])->toBe('estagnado');
});

it('manda subir 10% no multiarticular grande, 2% no músculo pequeno, e reduzir abaixo do mínimo', function () {
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();
    test()->putJson('/api/objetivo', ['goal' => 'hipertrofia'])->assertOk();

    lancarForca('agachamento', 12, 100, '2026-10-19 10:00:00');
    lancarForca('agachamento', 12, 100, '2026-10-20 10:00:00');
    $agachamento = collect(test()->getJson('/api/treino/progressao')->json())->firstWhere('exercise_code', 'agachamento');
    expect($agachamento['message'])->toBe('Suba a carga.');
    expect($agachamento['suggested_load_kg'])->toEqual(110);

    lancarForca('rosca-direta', 12, 20, '2026-10-20 11:00:00');
    lancarForca('rosca-direta', 12, 20, '2026-10-21 11:00:00');
    $rosca = collect(test()->getJson('/api/treino/progressao')->json())->firstWhere('exercise_code', 'rosca-direta');
    expect($rosca['suggested_load_kg'])->toEqual(20.4);

    lancarForca('desenvolvimento', 6, 40, '2026-10-22 10:00:00');
    $abaixo = collect(test()->getJson('/api/treino/progressao')->json())->firstWhere('exercise_code', 'desenvolvimento');
    expect($abaixo['message'])->toBe('Reduza a carga até o mínimo da faixa voltar.');
    expect($abaixo['state'])->toBeNull();
});
