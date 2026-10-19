<?php

namespace App\RegistroDeExercicio;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Sessao extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    protected $table = 'training_sessions';

    protected $fillable = [
        'user_id',
        'modality_code',
        'trained_on',
        'duration_min',
        'intensity_factor',
        'already_in_lifestyle',
        'net_kcal',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trained_on' => 'date',
            'duration_min' => 'integer',
            'intensity_factor' => 'float',
            'already_in_lifestyle' => 'boolean',
            'net_kcal' => 'float',
        ];
    }
}
