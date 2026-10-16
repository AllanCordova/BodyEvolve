<?php

namespace App\Objetivo;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $table = 'profiles';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'goal',
        'goal_started_on',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'goal_started_on' => 'date',
        ];
    }
}
