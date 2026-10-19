<?php

namespace App\Algoritmo;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Manutencao',
    required: ['basal_kcal', 'lifestyle'],
    properties: [
        new OA\Property(property: 'basal_kcal', type: 'integer', nullable: true),
        new OA\Property(
            property: 'lifestyle',
            type: 'string',
            enum: ['sedentaria', 'moderada', 'vigorosa'],
            nullable: true,
        ),
        new OA\Property(property: 'maintenance_kcal', type: 'integer'),
    ],
)]
class Contrato {}
