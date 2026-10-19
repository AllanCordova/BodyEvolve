<?php

namespace App\Perfil;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'profiles';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'age_years',
        'sex',
        'height_cm',
        'lifestyle',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'age_years' => 'integer',
            'height_cm' => 'integer',
        ];
    }
}
