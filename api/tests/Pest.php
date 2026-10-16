<?php

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
