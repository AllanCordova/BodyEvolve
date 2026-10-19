<?php

namespace App\Objetivo;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MetaNova',
    required: ['goal'],
    properties: [
        new OA\Property(
            property: 'goal',
            type: 'string',
            enum: ['hipertrofia', 'emagrecimento', 'forca', 'resistencia'],
        ),
    ],
)]
#[OA\Schema(
    schema: 'Meta',
    required: ['goal'],
    properties: [
        new OA\Property(
            property: 'goal',
            type: 'string',
            enum: ['hipertrofia', 'emagrecimento', 'forca', 'resistencia'],
            nullable: true,
        ),
        new OA\Property(property: 'goal_started_on', type: 'string', format: 'date'),
        new OA\Property(
            property: 'micronutrients',
            type: 'array',
            items: new OA\Items(type: 'string'),
        ),
        new OA\Property(property: 'hydration_reminder', type: 'string'),
        new OA\Property(property: 'kcal', type: 'integer', description: 'Goal calories. Strength uses maintenance plus 5 percent.'),
        new OA\Property(property: 'maintenance_kcal', type: 'integer'),
        new OA\Property(property: 'protein_g', type: 'number', format: 'float'),
        new OA\Property(property: 'fat_g', type: 'number', format: 'float'),
        new OA\Property(property: 'carb_g', type: 'number', format: 'float'),
        new OA\Property(property: 'carb_warning', type: 'string'),
    ],
)]
class Contrato {}
