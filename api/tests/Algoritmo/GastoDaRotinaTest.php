<?php

use App\Algoritmo\GastoDaRotina;

it('multiplica o basal pelos três pesos e arredonda para kcal', function () {
    expect(GastoDaRotina::kcal(1780, 'sedentaria'))->toBe(2723);
    expect(GastoDaRotina::kcal(1780, 'moderada'))->toBe(3133);
    expect(GastoDaRotina::kcal(1780, 'vigorosa'))->toBe(4005);
});

it('não calcula sem basal', function () {
    expect(GastoDaRotina::kcal(null, 'sedentaria'))->toBeNull();
    expect(GastoDaRotina::kcal(null, 'moderada'))->toBeNull();
    expect(GastoDaRotina::kcal(null, 'vigorosa'))->toBeNull();
});
