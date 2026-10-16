<?php

namespace App\Perfil;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RegistroDePeso extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    protected $table = 'weight_logs';

    protected $fillable = [
        'user_id',
        'weight_kg',
        'recorded_on',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight_kg' => 'float',
            'recorded_on' => 'date',
        ];
    }
}
