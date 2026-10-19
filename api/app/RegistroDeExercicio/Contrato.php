<?php

namespace App\RegistroDeExercicio;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SessaoNova',
    required: ['modality_code', 'duration_min', 'intensity_code', 'already_in_lifestyle'],
    properties: [
        new OA\Property(property: 'modality_code', type: 'string'),
        new OA\Property(property: 'duration_min', type: 'integer'),
        new OA\Property(property: 'intensity_code', type: 'string'),
        new OA\Property(property: 'already_in_lifestyle', type: 'boolean'),
        new OA\Property(property: 'trained_on', type: 'string', format: 'date'),
    ],
)]
#[OA\Schema(
    schema: 'Sessao',
    required: ['id', 'modality_code', 'modality_name', 'duration_min', 'intensity_code', 'intensity_factor', 'already_in_lifestyle', 'trained_on', 'net_kcal'],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'modality_code', type: 'string'),
        new OA\Property(property: 'modality_name', type: 'string'),
        new OA\Property(property: 'duration_min', type: 'integer'),
        new OA\Property(property: 'intensity_code', type: 'string'),
        new OA\Property(property: 'intensity_factor', type: 'number', format: 'float'),
        new OA\Property(property: 'already_in_lifestyle', type: 'boolean'),
        new OA\Property(property: 'trained_on', type: 'string', format: 'date'),
        new OA\Property(property: 'net_kcal', type: 'number', format: 'float'),
    ],
)]
#[OA\Schema(
    schema: 'Modalidade',
    required: ['code', 'name', 'catalog', 'intensities'],
    properties: [
        new OA\Property(property: 'code', type: 'string'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'catalog', type: 'string', enum: ['cardio', 'sports']),
        new OA\Property(
            property: 'intensities',
            type: 'array',
            items: new OA\Items(
                required: ['code', 'name', 'factor'],
                properties: [
                    new OA\Property(property: 'code', type: 'string'),
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'factor', type: 'number', format: 'float'),
                ],
                type: 'object',
            ),
        ),
    ],
)]
class Contrato {}
