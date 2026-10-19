<?php

namespace App\RegistroDeExercicio;

class Catalogo
{
    /**
     * @return list<array{code: string, name: string, catalog: string, base: float, counts_as_strength: bool, anchors: list<array{code: string, name: string, factor: float}>}>
     */
    public static function modalidades(?string $catalogo = null): array
    {
        $lista = self::linhas();

        if ($catalogo === null) {
            return $lista;
        }

        return array_values(array_filter(
            $lista,
            fn (array $modalidade): bool => $modalidade['catalog'] === $catalogo,
        ));
    }

    /**
     * @return array{code: string, name: string, catalog: string, base: float, counts_as_strength: bool, anchors: list<array{code: string, name: string, factor: float}>}|null
     */
    public static function modalidade(string $codigo): ?array
    {
        foreach (self::linhas() as $modalidade) {
            if ($modalidade['code'] === $codigo) {
                return $modalidade;
            }
        }

        return null;
    }

    public static function fator(string $codigo, string $intensidade): ?float
    {
        if ($intensidade === 'padrao') {
            return 1.0;
        }

        $modalidade = self::modalidade($codigo);

        if ($modalidade === null) {
            return null;
        }

        foreach ($modalidade['anchors'] as $ancora) {
            if ($ancora['code'] === $intensidade) {
                return $ancora['factor'];
            }
        }

        return null;
    }

    /**
     * @param  array{code: string, name: string, anchors: list<array{code: string, name: string, factor: float}>}  $modalidade
     * @return list<array{code: string, name: string, factor: float}>
     */
    public static function intensidades(array $modalidade): array
    {
        return [
            ['code' => 'padrao', 'name' => 'Padrão', 'factor' => 1.0],
            ...$modalidade['anchors'],
        ];
    }

    /**
     * @return list<array{code: string, name: string, catalog: string, base: float, counts_as_strength: bool, anchors: list<array{code: string, name: string, factor: float}>}>
     */
    private static function linhas(): array
    {
        return [
            ...self::cardio(),
            ...self::esportes(),
        ];
    }

    /**
     * @param  list<array{0: string, 1: string, 2: float}>  $ancoras
     * @return list<array{code: string, name: string, factor: float}>
     */
    private static function ancoras(array $ancoras): array
    {
        return array_map(
            fn (array $ancora): array => [
                'code' => $ancora[0],
                'name' => $ancora[1],
                'factor' => $ancora[2],
            ],
            $ancoras,
        );
    }

    /**
     * @param  list<array{0: string, 1: string, 2: float}>  $ancoras
     * @return array{code: string, name: string, catalog: string, base: float, counts_as_strength: bool, anchors: list<array{code: string, name: string, factor: float}>}
     */
    private static function linha(string $catalogo, string $codigo, string $nome, float $base, bool $forca, array $ancoras = []): array
    {
        return [
            'code' => $codigo,
            'name' => $nome,
            'catalog' => $catalogo,
            'base' => $base,
            'counts_as_strength' => $forca,
            'anchors' => self::ancoras($ancoras),
        ];
    }

    /**
     * @return list<array{code: string, name: string, catalog: string, base: float, counts_as_strength: bool, anchors: list<array{code: string, name: string, factor: float}>}>
     */
    private static function cardio(): array
    {
        return [
            self::linha('cardio', 'caminhada-rapida', 'Caminhada rápida', 4.8, false, [
                ['moderada', 'Moderada', 0.79],
                ['muito-rapida', 'Muito rápida', 1.15],
            ]),
            self::linha('cardio', 'corrida', 'Corrida ou jogging', 7.5, false, [
                ['oito-km-h', 'Cerca de 8 km/h', 1.13],
                ['ritmo-livre', 'Ritmo livre de corrida', 1.40],
            ]),
            self::linha('cardio', 'ciclismo', 'Ciclismo ao ar livre', 7.0, false, [
                ['facil', 'Fácil', 0.61],
                ['vigoroso', 'Vigoroso', 1.29],
            ]),
            self::linha('cardio', 'pular-corda', 'Pular corda', 11.0, false, [
                ['lento', 'Lento', 0.75],
                ['rapido', 'Rápido', 1.12],
            ]),
            self::linha('cardio', 'subir-escadas', 'Subir escadas', 6.8, false, [
                ['lento', 'Lento', 0.66],
                ['rapido', 'Rápido', 1.37],
            ]),
            self::linha('cardio', 'danca', 'Dança ou Zumba', 6.5, false, [
                ['video', 'Vídeo em casa', 0.85],
                ['aerobica', 'Dança aeróbica', 1.12],
            ]),
            self::linha('cardio', 'circuito', 'Circuito com peso corporal', 6.0, false, [
                ['vigoroso', 'Movimentos vigorosos', 1.25],
                ['hiit', 'HIIT', 1.83],
            ]),
            self::linha('cardio', 'esteira', 'Esteira, caminhada rápida', 4.8, false, [
                ['corrida', 'Corrida na esteira', 1.56],
            ]),
            self::linha('cardio', 'eliptico', 'Elíptico', 5.0, false, [
                ['vigoroso', 'Vigoroso', 1.80],
            ]),
            self::linha('cardio', 'ergometrica', 'Bicicleta ergométrica', 6.8, false, [
                ['cinquenta-w', '50 W', 0.59],
                ['spinning', 'Spinning', 1.32],
            ]),
            self::linha('cardio', 'remo', 'Máquina de remo', 7.3, false, [
                ['abaixo-100w', 'Abaixo de 100 W', 0.68],
                ['cento-cinquenta-w', '150 a 199 W', 1.51],
            ]),
            self::linha('cardio', 'hiit', 'HIIT', 11.0, false, [
                ['intervalado', 'Intervalado moderado', 0.64],
            ]),
            self::linha('cardio', 'hidroginastica', 'Hidroginástica', 5.5, false, [
                ['alta', 'Alta intensidade', 1.36],
            ]),
        ];
    }

    /**
     * @return list<array{code: string, name: string, catalog: string, base: float, counts_as_strength: bool, anchors: list<array{code: string, name: string, factor: float}>}>
     */
    private static function esportes(): array
    {
        return [
            self::linha('sports', 'futebol', 'Futebol', 7.0, false, [['competitivo', 'Competitivo', 1.36]]),
            self::linha('sports', 'basquetebol', 'Basquetebol', 7.5, false, [
                ['sem-jogo', 'Sem jogo', 0.80],
                ['jogo', 'Jogo', 1.07],
            ]),
            self::linha('sports', 'futsal', 'Futsal', 7.8, false),
            self::linha('sports', 'handebol', 'Handebol', 8.0, false),
            self::linha('sports', 'futebol-americano', 'Futebol americano', 8.0, false, [['toque', 'Toque leve', 0.50]]),
            self::linha('sports', 'rugby', 'Rugby', 8.3, false, [['touch', 'Touch', 0.76]]),
            self::linha('sports', 'polo-aquatico', 'Polo aquático', 10.0, false),
            self::linha('sports', 'hoquei-gelo', 'Hóquei no gelo', 8.0, false, [['competitivo', 'Competitivo', 1.25]]),
            self::linha('sports', 'hoquei-grama', 'Hóquei de grama', 7.8, false),
            self::linha('sports', 'natacao', 'Natação, crawl médio', 8.0, false, [
                ['lento', 'Crawl lento', 0.72],
                ['rapido', 'Crawl rápido', 1.23],
            ]),
            self::linha('sports', 'atletismo-corridas', 'Atletismo, corridas', 10.0, false, [['jogging', 'Jogging geral', 0.75]]),
            self::linha('sports', 'atletismo-saltos', 'Atletismo, saltos', 6.0, false),
            self::linha('sports', 'atletismo-lancamentos', 'Atletismo, lançamentos', 4.0, false),
            self::linha('sports', 'ciclismo-esporte', 'Ciclismo', 7.0, false, [
                ['facil', 'Fácil', 0.61],
                ['vigoroso', 'Vigoroso', 1.29],
            ]),
            self::linha('sports', 'halterofilismo', 'Halterofilismo', 5.0, true, [['vigoroso', 'Potência vigorosa', 1.20]]),
            self::linha('sports', 'remo-agua', 'Remo na água', 5.8, false, [
                ['competicao', 'Competição', 2.07],
                ['single', 'Single scull em prova', 2.67],
            ]),
            self::linha('sports', 'canoagem', 'Canoagem', 5.8, false, [
                ['caiaque', 'Caiaque moderado', 0.86],
                ['competicao', 'Competição', 2.16],
            ]),
            self::linha('sports', 'voleibol', 'Voleibol', 6.0, false, [['recreativo', 'Recreativo', 0.50]]),
            self::linha('sports', 'tenis', 'Tênis', 6.8, false, [
                ['duplas', 'Duplas', 0.66],
                ['simples', 'Simples', 1.18],
            ]),
            self::linha('sports', 'tenis-de-mesa', 'Tênis de mesa', 4.0, false),
            self::linha('sports', 'volei-de-praia', 'Vôlei de praia', 8.0, false),
            self::linha('sports', 'badminton', 'Badminton', 5.5, false, [
                ['competitivo', 'Competitivo', 1.27],
                ['partida', 'Partida', 1.64],
            ]),
            self::linha('sports', 'squash', 'Squash', 7.3, false, [['jogo', 'Jogo', 1.64]]),
            self::linha('sports', 'padel', 'Padel', 6.0, false),
            self::linha('sports', 'futevolei', 'Futevôlei', 8.0, false),
            self::linha('sports', 'judo', 'Judô', 11.3, false, [['iniciante', 'Iniciante', 0.47]]),
            self::linha('sports', 'boxe', 'Boxe', 7.8, false, [
                ['saco', 'Saco', 0.74],
                ['ringue', 'No ringue', 1.58],
            ]),
            self::linha('sports', 'karate', 'Karatê', 10.3, false, [['iniciante', 'Iniciante', 0.51]]),
            self::linha('sports', 'taekwondo', 'Taekwondo', 10.3, false, [['combate', 'Simulação de combate', 1.39]]),
            self::linha('sports', 'jiu-jitsu', 'Jiu-jitsu', 10.3, false, [['iniciante', 'Iniciante', 0.51]]),
            self::linha('sports', 'muay-thai', 'Muay Thai', 10.3, false, [['kickboxing', 'Kickboxing', 0.71]]),
            self::linha('sports', 'mma', 'MMA', 10.3, false),
            self::linha('sports', 'esgrima', 'Esgrima', 6.0, false, [['competitiva', 'Espada competitiva', 1.63]]),
            self::linha('sports', 'luta-olimpica', 'Luta olímpica', 6.0, false),
            self::linha('sports', 'golfe', 'Golfe', 4.5, false, [['carrinho', 'Com carrinho', 0.78]]),
            self::linha('sports', 'tiro-com-arco', 'Tiro com arco', 4.3, false),
            self::linha('sports', 'boliche', 'Boliche', 3.8, false),
            self::linha('sports', 'sinuca', 'Sinuca ou bilhar', 2.5, false),
            self::linha('sports', 'tiro-esportivo', 'Tiro esportivo', 2.5, false),
            self::linha('sports', 'ginastica-artistica', 'Ginástica artística', 3.8, false),
            self::linha('sports', 'ginastica-ritmica', 'Ginástica rítmica', 3.8, false),
            self::linha('sports', 'surfe', 'Surfe', 3.0, false, [
                ['competitivo', 'Competitivo', 1.67],
                ['remada', 'Só a remada', 2.27],
            ]),
            self::linha('sports', 'saltos-ornamentais', 'Saltos ornamentais', 3.0, false),
            self::linha('sports', 'skate', 'Skate', 5.0, false, [['competitivo', 'Competitivo', 1.20]]),
            self::linha('sports', 'nado-artistico', 'Nado artístico', 8.0, false),
            self::linha('sports', 'esqui', 'Esqui alpino', 6.3, false, [
                ['leve', 'Leve', 0.68],
                ['vigoroso', 'Vigoroso', 1.27],
            ]),
            self::linha('sports', 'snowboard', 'Snowboard', 7.5, false, [
                ['leve', 'Leve', 0.57],
                ['vigoroso', 'Vigoroso', 1.07],
            ]),
            self::linha('sports', 'hipismo', 'Hipismo', 5.5, false, [
                ['passo', 'Passo', 0.69],
                ['galope', 'Galope', 1.33],
            ]),
            self::linha('sports', 'beisebol', 'Beisebol', 5.0, false),
            self::linha('sports', 'criquete', 'Críquete', 4.8, false),
        ];
    }
}
