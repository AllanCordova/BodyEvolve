<?php

namespace App\RegistroDeExercicio;

class Exercicios
{
    /**
     * @return array{code: string, name: string, multi_joint: bool, large_muscle: bool, muscle_group: string}|null
     */
    public static function um(string $codigo): ?array
    {
        return self::catalogo()[$codigo] ?? null;
    }

    /**
     * @return list<array{code: string, name: string, multi_joint: bool, large_muscle: bool, muscle_group: string}>
     */
    public static function todos(): array
    {
        return array_values(self::catalogo());
    }

    /**
     * @return array<string, array{code: string, name: string, multi_joint: bool, large_muscle: bool, muscle_group: string}>
     */
    private static function catalogo(): array
    {
        $linhas = [
            ['agachamento', 'Agachamento', true, true, 'pernas'],
            ['levantamento-terra', 'Levantamento terra', true, true, 'posterior'],
            ['supino', 'Supino', true, true, 'peito'],
            ['desenvolvimento', 'Desenvolvimento', true, true, 'ombros'],
            ['remada', 'Remada', true, true, 'costas'],
            ['barra-fixa', 'Barra fixa', true, true, 'costas'],
            ['leg-press', 'Leg press', true, true, 'pernas'],
            ['afundo', 'Afundo', true, true, 'pernas'],
            ['flexao', 'Flexão', true, true, 'peito'],
            ['rosca-direta', 'Rosca direta', false, false, 'biceps'],
            ['elevacao-lateral', 'Elevação lateral', false, false, 'ombros'],
            ['abdominal', 'Abdominal', false, false, 'abdomen'],
        ];

        $catalogo = [];

        foreach ($linhas as [$codigo, $nome, $multi, $grande, $grupo]) {
            $catalogo[$codigo] = [
                'code' => $codigo,
                'name' => $nome,
                'multi_joint' => $multi,
                'large_muscle' => $grande,
                'muscle_group' => $grupo,
            ];
        }

        return $catalogo;
    }
}
