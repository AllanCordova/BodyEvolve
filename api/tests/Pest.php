<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->in(
    'Feature',
    'Acesso',
    'Objetivo',
    'Perfil',
    'RegistroDeExercicio',
    'RegistroDeKcal',
    'Algoritmo',
);

uses(RefreshDatabase::class)->in('Acesso', 'Perfil', 'Algoritmo', 'Objetivo', 'RegistroDeExercicio', 'RegistroDeKcal');
