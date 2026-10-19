<?php

namespace App\RegistroDeKcal;

use Illuminate\Database\Eloquent\Model;

class Comida extends Model
{
    protected $table = 'daily_intakes';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'intake_date',
        'kcal',
        'protein_g',
        'fat_g',
        'carb_g',
        'fiber_g',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'intake_date' => 'date',
            'kcal' => 'integer',
            'protein_g' => 'float',
            'fat_g' => 'float',
            'carb_g' => 'float',
            'fiber_g' => 'float',
        ];
    }
}
