<?php

namespace App\Algoritmo;

use App\Models\User;
use App\Objetivo\Meta;
use App\Perfil\Corpo;
use App\Perfil\Perfil;
use App\RegistroDeExercicio\Catalogo;
use App\RegistroDeExercicio\Exercicios;
use App\RegistroDeExercicio\Lancamento;
use App\RegistroDeExercicio\Sessao;
use App\RegistroDeExercicio\Sessoes;
use App\RegistroDeKcal\Comida;
use Carbon\Carbon;

class RotaDoDia
{
    /**
     * @return list<array{kind: string, message: string, fails_goal: bool}>
     */
    public static function alertas(User $usuario, ?string $dia = null): array
    {
        $dia ??= now()->toDateString();
        $meta = Meta::query()->find($usuario->id)?->goal;
        $alertas = [];

        if ($meta === null) {
            return $alertas;
        }

        $corpo = Corpo::resposta($usuario);
        $manutencao = Manutencao::kcal(
            $corpo['gasto_repouso_kcal'] ?? null,
            Perfil::query()->find($usuario->id)?->lifestyle,
            Sessoes::liquidas($usuario->id, $dia),
        );
        $comida = Comida::query()->where('user_id', $usuario->id)->whereDate('intake_date', $dia)->first();
        $alvo = AlvoDaMeta::de($meta, $manutencao, $corpo['weight_kg']);

        if ($meta === 'hipertrofia' && $comida !== null && $manutencao !== null && $comida->kcal < $manutencao) {
            $alertas[] = self::alerta(
                'deficit_na_hipertrofia',
                'Suba a comida. Essa meta pede 15% acima da manutenção.',
            );
        }

        if (in_array($meta, ['hipertrofia', 'forca'], true)) {
            $alertas = [...$alertas, ...self::estimulo($usuario->id, $meta, $dia)];
        }

        if (in_array($meta, ['forca', 'resistencia'], true) && isset($alvo['carb_warning'])) {
            $alertas[] = self::alerta('carboidrato_nao_coube', $alvo['carb_warning']);
        }

        return $alertas;
    }

    /**
     * @return list<array{kind: string, message: string, fails_goal: bool}>
     */
    private static function estimulo(string $usuarioId, string $meta, string $dia): array
    {
        $inicio = Carbon::parse($dia)->startOfWeek()->toDateString();
        $fim = Carbon::parse($dia)->endOfWeek()->toDateString();
        $sessoes = Sessao::query()
            ->where('user_id', $usuarioId)
            ->whereDate('trained_on', '>=', $inicio)
            ->whereDate('trained_on', '<=', $fim)
            ->get();
        $deForca = $sessoes->filter(function (Sessao $sessao): bool {
            return Catalogo::modalidade($sessao->modality_code)['counts_as_strength'] ?? false;
        })->count();

        $alertas = [];

        if ($deForca === 0) {
            $alertas[] = self::alerta(
                'sem_treino_de_forca',
                'Caminhada e cardio não substituem o treino de força.',
            );
        }

        if ($deForca === 1) {
            $alertas[] = self::alerta(
                'frequencia_baixa',
                'Ainda é a primeira sessão da semana. A frequência pede pelo menos duas.',
                false,
            );
        }

        if (self::tudoEstagnado($usuarioId, $meta)) {
            $alertas[] = self::alerta('carga_estagnada', 'A carga não avançou.');
        }

        if ($meta === 'hipertrofia') {
            foreach (self::volumesBaixos($usuarioId, $inicio, $fim) as $grupo) {
                $alertas[] = self::alerta('volume_baixo', "Volume baixo em {$grupo}.");
            }
        }

        return $alertas;
    }

    private static function tudoEstagnado(string $usuarioId, string $meta): bool
    {
        $vistos = false;

        foreach (Exercicios::todos() as $exercicio) {
            if (! $exercicio['multi_joint']) {
                continue;
            }

            $ultimos = Lancamento::query()
                ->where('user_id', $usuarioId)
                ->where('exercise_code', $exercicio['code'])
                ->orderByDesc('recorded_at')
                ->limit(2)
                ->get();

            if ($ultimos->count() < 2) {
                continue;
            }

            $vistos = true;
            $leitura = Progressao::de(
                $meta,
                [
                    'reps_done' => $ultimos->last()->reps_done,
                    'load_kg' => $ultimos->last()->load_kg,
                    'variation' => $ultimos->last()->variation,
                ],
                [
                    'reps_done' => $ultimos->first()->reps_done,
                    'load_kg' => $ultimos->first()->load_kg,
                    'variation' => $ultimos->first()->variation,
                ],
                $exercicio,
            );

            if ($leitura['state'] !== 'estagnado') {
                return false;
            }
        }

        return $vistos;
    }

    /**
     * @return list<string>
     */
    private static function volumesBaixos(string $usuarioId, string $inicio, string $fim): array
    {
        $series = [];
        $lancamentos = Lancamento::query()
            ->where('user_id', $usuarioId)
            ->whereDate('recorded_at', '>=', $inicio)
            ->whereDate('recorded_at', '<=', $fim)
            ->get();

        foreach ($lancamentos as $lancamento) {
            if ($lancamento->reps_done < 8 || $lancamento->reps_done > 12) {
                continue;
            }

            $grupo = Exercicios::um($lancamento->exercise_code)['muscle_group'] ?? null;

            if ($grupo === null) {
                continue;
            }

            $series[$grupo] = ($series[$grupo] ?? 0) + $lancamento->sets_done;
        }

        $baixos = [];

        foreach ($series as $grupo => $total) {
            if ($total < 10) {
                $baixos[] = $grupo;
            }
        }

        return $baixos;
    }

    /**
     * @return array{kind: string, message: string, fails_goal: bool}
     */
    private static function alerta(string $tipo, string $mensagem, bool $reprova = true): array
    {
        return [
            'kind' => $tipo,
            'message' => $mensagem,
            'fails_goal' => $reprova,
        ];
    }
}
