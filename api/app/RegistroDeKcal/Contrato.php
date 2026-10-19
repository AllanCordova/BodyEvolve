<?php

namespace App\RegistroDeKcal;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ComidaNova',
    required: ['kcal', 'protein_g', 'fat_g', 'carb_g', 'fiber_g'],
    properties: [
        new OA\Property(property: 'intake_date', type: 'string', format: 'date'),
        new OA\Property(property: 'kcal', type: 'integer'),
        new OA\Property(property: 'protein_g', type: 'number', format: 'float'),
        new OA\Property(property: 'fat_g', type: 'number', format: 'float'),
        new OA\Property(property: 'carb_g', type: 'number', format: 'float'),
        new OA\Property(property: 'fiber_g', type: 'number', format: 'float'),
    ],
)]
#[OA\Schema(
    schema: 'Comida',
    required: ['intake_date', 'kcal', 'protein_g', 'fat_g', 'carb_g', 'fiber_g'],
    properties: [
        new OA\Property(property: 'intake_date', type: 'string', format: 'date'),
        new OA\Property(property: 'kcal', type: 'integer', nullable: true),
        new OA\Property(property: 'protein_g', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'fat_g', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'carb_g', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'fiber_g', type: 'number', format: 'float', nullable: true),
    ],
)]
class Contrato {}
