<?php

namespace App\Algoritmo;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    use HasUuids;

    protected $table = 'alerts';

    protected $fillable = [
        'user_id',
        'alert_date',
        'kind',
        'message',
        'fails_goal',
        'resolved',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'alert_date' => 'date',
            'fails_goal' => 'boolean',
            'resolved' => 'boolean',
        ];
    }
}
