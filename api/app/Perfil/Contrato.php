<?php

namespace App\Perfil;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CorpoNovo',
    required: ['age_years', 'sex', 'height_cm', 'weight_kg'],
    properties: [
        new OA\Property(property: 'age_years', type: 'integer'),
        new OA\Property(property: 'sex', type: 'string', enum: ['masculino', 'feminino']),
        new OA\Property(property: 'height_cm', type: 'integer', description: 'Centimeters'),
        new OA\Property(property: 'weight_kg', type: 'number', format: 'float', description: 'Kilograms'),
    ],
)]
#[OA\Schema(
    schema: 'Corpo',
    required: ['age_years', 'sex', 'height_cm', 'weight_kg'],
    properties: [
        new OA\Property(property: 'age_years', type: 'integer', nullable: true),
        new OA\Property(property: 'sex', type: 'string', enum: ['masculino', 'feminino'], nullable: true),
        new OA\Property(property: 'height_cm', type: 'integer', nullable: true),
        new OA\Property(property: 'weight_kg', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'gasto_repouso_kcal', type: 'integer'),
    ],
)]
class Contrato {}
