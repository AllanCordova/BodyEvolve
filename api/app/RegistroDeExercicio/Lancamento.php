<?php

namespace App\RegistroDeExercicio;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Lancamento extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    public const CREATED_AT = 'recorded_at';

    protected $table = 'exercise_records';

    protected $fillable = [
        'session_id',
        'user_id',
        'exercise_code',
        'sets_done',
        'reps_done',
        'load_kg',
        'variation',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sets_done' => 'integer',
            'reps_done' => 'integer',
            'load_kg' => 'float',
            'recorded_at' => 'datetime',
        ];
    }
}
