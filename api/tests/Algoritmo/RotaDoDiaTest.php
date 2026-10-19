<?php

use App\Algoritmo\Alerta;
use Carbon\Carbon;

beforeEach(function () {
    comoCliente();
    Carbon::setTestNow('2026-10-19 10:00:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

function preparar(string $meta, string $rotina = 'sedentaria'): void
{
    usuarioComSenha('Ana', 'ana@example.com');
    entrarCom('ana@example.com', 'senha-segura');
    test()->putJson('/api/perfil', medidasDoCorpo())->assertOk();
    test()->putJson('/api/perfil/rotina', ['lifestyle' => $rotina])->assertOk();
    test()->putJson('/api/objetivo', ['goal' => $meta])->assertOk();
}

function sessao(string $modalidade, array $exercicios = []): void
{
    test()->postJson('/api/treino/sessoes', [
        'modality_code' => $modalidade,
        'duration_min' => 40,
        'intensity_code' => 'padrao',
        'already_in_lifestyle' => false,
        ...($exercicios === [] ? [] : ['exercises' => $exercicios]),
    ])->assertCreated();
}

it('avisa hipertrofia abaixo da manutenção e some quando a comida sobe', function () {
    preparar('hipertrofia');

    test()->putJson('/api/comida', [
        'kcal' => 2000,
        'protein_g' => 160,
        'fat_g' => 70,
        'carb_g' => 180,
        'fiber_g' => 20,
    ])->assertOk();

    test()->getJson('/api/alertas')->assertJsonFragment([
        'kind' => 'deficit_na_hipertrofia',
        'message' => 'Suba a comida. Essa meta pede 15% acima da manutenção.',
    ]);

    test()->putJson('/api/comida', [
        'kcal' => 4000,
        'protein_g' => 160,
        'fat_g' => 70,
        'carb_g' => 400,
        'fiber_g' => 20,
    ])->assertOk();

    $tipos = collect(test()->getJson('/api/alertas')->json())->pluck('kind');
    expect($tipos)->not->toContain('deficit_na_hipertrofia');
    expect(Alerta::query()->where('kind', 'deficit_na_hipertrofia')->count())->toBe(0);
});

it('avisa cardio no lugar da força, a primeira sessão e a carga parada', function () {
    preparar('forca');
    sessao('caminhada-rapida');

    test()->getJson('/api/alertas')->assertJsonFragment([
        'kind' => 'sem_treino_de_forca',
        'message' => 'Caminhada e cardio não substituem o treino de força.',
    ]);

    sessao('musculacao', [[
        'exercise_code' => 'agachamento',
        'sets_done' => 3,
        'reps_done' => 8,
        'load_kg' => 100,
    ]]);

    $primeira = collect(test()->getJson('/api/alertas')->json());
    expect($primeira->pluck('kind'))->not->toContain('sem_treino_de_forca');
    expect($primeira->firstWhere('kind', 'frequencia_baixa')['fails_goal'])->toBeFalse();

    Carbon::setTestNow('2026-10-20 10:00:00');
    sessao('musculacao', [[
        'exercise_code' => 'agachamento',
        'sets_done' => 3,
        'reps_done' => 8,
        'load_kg' => 100,
    ]]);

    Carbon::setTestNow('2026-10-19 10:00:00');
    test()->getJson('/api/alertas')->assertJsonFragment([
        'kind' => 'carga_estagnada',
        'message' => 'A carga não avançou.',
    ]);
});

it('avisa volume baixo na hipertrofia e carboidrato que não coube', function () {
    preparar('hipertrofia');
    sessao('musculacao', [[
        'exercise_code' => 'supino',
        'sets_done' => 3,
        'reps_done' => 10,
        'load_kg' => 60,
    ]]);

    test()->getJson('/api/alertas')->assertJsonFragment([
        'kind' => 'volume_baixo',
        'message' => 'Volume baixo em peito.',
    ]);

    test()->putJson('/api/objetivo', ['goal' => 'resistencia'])->assertOk();
    test()->getJson('/api/alertas')->assertJsonFragment(['kind' => 'carboidrato_nao_coube']);

    test()->putJson('/api/objetivo', ['goal' => 'emagrecimento'])->assertOk();
    expect(test()->getJson('/api/objetivo')->json('fat_g'))->toBeGreaterThanOrEqual(0.6 * 80);
});
