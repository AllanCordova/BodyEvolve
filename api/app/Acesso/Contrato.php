<?php

namespace App\Acesso;

use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', title: 'BodyEvolve')]
#[OA\Schema(
    schema: 'Credenciais',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email'),
        new OA\Property(property: 'password', type: 'string', format: 'password'),
    ],
)]
#[OA\Schema(
    schema: 'ContaNova',
    required: ['name', 'email', 'password'],
    properties: [
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'email', type: 'string', format: 'email'),
        new OA\Property(property: 'password', type: 'string', format: 'password'),
    ],
)]
#[OA\Schema(
    schema: 'Sessao',
    required: ['name', 'email'],
    properties: [
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'email', type: 'string', format: 'email'),
        new OA\Property(property: 'icon_url', type: 'string', nullable: true),
    ],
)]
class Contrato {}
