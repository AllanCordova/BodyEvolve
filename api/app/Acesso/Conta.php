<?php

namespace App\Acesso;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class Conta
{
    /**
     * @return array{name: string, email: string, icon_url: string|null}
     */
    public static function resposta(User $usuario): array
    {
        return [
            'name' => $usuario->name,
            'email' => $usuario->email,
            'icon_url' => $usuario->icon_path
                ? Storage::disk('public')->url($usuario->icon_path)
                : null,
        ];
    }
}
