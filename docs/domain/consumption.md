# Consumo energético das atividades

Base para o gasto de uma sessão. A fonte é o [2024 Adult Compendium of Physical Activities](https://pacompendium.com/) (Ainsworth et al.), adultos de 19 a 59 anos. O compêndio publica o custo em MET. Na convenção usada aqui, **1 MET = 1 kcal por kg em 60 minutos**.

A intensidade padrão é o código *general*, *casual*, *social* ou *moderate* da modalidade. Quando o texto de [cardio](cardio.md), [sports](sports.md) ou [strong](strong.md) descreve a forma vigorosa (HIIT, pular corda, circuito vigoroso), a base é esse código. Códigos de competição e de esforço vigoroso não entram na base: viram âncora de intensidade.

## Fórmula

A tabela abaixo é o gasto de **60 minutos na intensidade padrão, por quilograma**. Peso, tempo e intensidade entram como multiplicadores:

```text
kcal = base × peso_kg × (minutos / 60) × fator_intensidade
```

- `base` é a coluna **kcal/kg em 60 min**. Na intensidade padrão, `fator_intensidade = 1`.
- `fator_intensidade = MET_da_ancora / base`. Use só âncora publicada nesta tabela. Sem âncora, o fator permanece 1.
- Os minutos são o tempo naquela intensidade. Pausa e intervalo fora de esforço não entram nos minutos.

Exemplo, caminhada rápida (`base = 4.8`), 80 kg, 45 min, intensidade padrão:

```text
4.8 × 80 × (45 / 60) × 1 = 288 kcal
```

A mesma sessão no ritmo muito rápido (âncora 5.5 MET, fator `5.5 / 4.8 = 1.15`):

```text
4.8 × 80 × (45 / 60) × 1.15 = 331 kcal
```

A coluna **70 kg, 60 min** é só leitura. Não use 70 kg como peso fixo do produto: o peso vem do perfil.

### Bruto e líquido

O MET do compêndio é **bruto**: inclui o repouso daquela hora. A equação de Mifflin-St Jeor em [nutritional-plan](nutritional-plan.md) já cobre o repouso do dia. Para somar a sessão em cima do custo basal, use o gasto líquido:

```text
kcal_liquidas = (base - 1) × peso_kg × (minutos / 60) × fator_intensidade
```

No exemplo da caminhada, o líquido é `(4.8 - 1) × 80 × 0.75 = 228 kcal`. Se o dia inteiro já entrou por um multiplicador de estilo de vida, não some de novo estas horas.

### Vínculo com o compêndio

- **direto:** a modalidade tem código próprio.
- **correspondência:** não há código com o nome da modalidade; a base é o código cuja descrição cobre o esforço.
- **proxy:** modalidade diferente, usada só porque não existe medição. Não tratar como dado da modalidade.

Não há média inventada entre códigos. O triatlo não tem MET único: vale a etapa que está sendo feita.

## Cardio

Fonte das modalidades: [cardio](cardio.md).

| Modalidade | Código | kcal/kg em 60 min | 70 kg, 60 min | Vínculo | Intensidade |
| --- | ---: | ---: | ---: | --- | --- |
| Caminhada rápida (5,6–6,3 km/h) | 17200 | 4.8 | 336 | direto | moderada 0.79 (3.8 MET, 17190); muito rápida 1.15 (5.5 MET, 17220) |
| Corrida ou jogging, ritmo livre | 12020 | 7.5 | 525 | direto | ~8 km/h 1.13 (8.5 MET, 12030); ritmo livre de corrida 1.40 (10.5 MET, 12145) |
| Ciclismo ao ar livre | 01014 | 7.0 | 490 | direto | fácil 0.61 (4.3 MET, 01015); vigoroso 1.29 (9.0 MET, 01017) |
| Pular corda | 02068 | 11.0 | 770 | direto | lento 0.75 (8.3 MET, 15552); rápido 1.12 (12.3 MET, 15550) |
| Subir escadas | 17131 | 6.8 | 476 | direto | lento 0.66 (4.5 MET, 17133); rápido 1.37 (9.3 MET, 17134) |
| Dança / Zumba, aula em grupo | 02310 | 6.5 | 455 | direto | vídeo em casa 0.85 (5.5 MET, 02315); dança aeróbica geral 1.12 (7.3 MET, 02000) |
| Circuito com peso corporal (burpee, polichinelo, mountain climber) | 02032 | 6.0 | 420 | direto | movimentos vigorosos 1.25 (7.5 MET, 02020); em formato HIIT 1.83 (11.0 MET, 02214) |
| Esteira, caminhada rápida, 0% de inclinação | 17358 | 4.8 | 336 | direto | corrida na esteira usa a base de corrida (7.5); fator sobre esta base 1.56 |
| Elíptico | 02048 | 5.0 | 350 | direto | vigoroso 1.80 (9.0 MET, 02049) |
| Bicicleta ergométrica | 01200 | 6.8 | 476 | direto | 50 W 0.59 (4.0 MET, 01214); spinning 1.32 (9.0 MET, 01270) |
| Máquina de remo | 02070 | 7.3 | 511 | direto | abaixo de 100 W 0.68 (5.0 MET, 02071); 150–199 W 1.51 (11.0 MET, 02073) |
| HIIT (burpee, mountain climber, salto, Tabata) | 02214 | 11.0 | 770 | direto | intervalado moderado 0.64 (7.0 MET, 02210) |
| Hidroginástica | 18355 | 5.5 | 385 | direto | alta intensidade 1.36 (7.5 MET, 18358) |

A máquina de remo e o remo na água não compartilham base. Aula de spinning não substitui a base da ergométrica: entra pelo fator 1.32.

## Esportes

Fonte das modalidades: [sports](sports.md). Partida recreativa ou treino moderado é a base. Jogo competitivo sobe pelo fator da âncora.

| Modalidade | Código | kcal/kg em 60 min | 70 kg, 60 min | Vínculo | Intensidade |
| --- | ---: | ---: | ---: | --- | --- |
| Futebol | 15610 | 7.0 | 490 | direto | competitivo 1.36 (9.5 MET, 15605) |
| Basquetebol | 15055 | 7.5 | 525 | direto | sem jogo 0.80 (6.0 MET, 15050); jogo 1.07 (8.0 MET, 15040) |
| Futsal | 15195 | 7.8 | 546 | direto | código único |
| Handebol (equipe) | 15330 | 8.0 | 560 | direto | 12.0 MET (15320) é handebol de parede, não esta modalidade |
| Futebol americano | 15210 | 8.0 | 560 | direto | toque leve 0.50 (4.0 MET, 15232) |
| Rugby | 15560 | 8.3 | 581 | direto | touch 0.76 (6.3 MET, 15562) |
| Polo aquático | 18360 | 10.0 | 700 | direto | código único |
| Hóquei no gelo | 15360 | 8.0 | 560 | direto | competitivo 1.25 (10.0 MET, 15362) |
| Hóquei de grama | 15350 | 7.8 | 546 | direto | código único |
| Natação, crawl médio (~50 jardas/min) | 18290 | 8.0 | 560 | direto | crawl lento 0.72 (5.8 MET, 18240); crawl rápido 1.23 (9.8 MET, 18230) |
| Atletismo, corridas (treino de pista) | 12180 | 10.0 | 700 | direto | se a sessão for jogging geral, fator 0.75 (7.5 MET, 12020) |
| Atletismo, saltos (altura, distância, triplo, dardo, vara) | 15733 | 6.0 | 420 | direto | código do grupo de provas |
| Atletismo, lançamentos (peso, disco, martelo) | 15732 | 4.0 | 280 | direto | código do grupo de provas |
| Ciclismo | 01014 | 7.0 | 490 | direto | mesma base do ciclismo em cardio |
| Halterofilismo | 02052 | 5.0 | 350 | correspondência | não há código de arranco e arremesso; âncora vigorosa de potência 1.20 (6.0 MET, 02050) |
| Remo na água, ritmo moderado (6,4–9,5 km/h) | 18050 | 5.8 | 406 | direto | crew ou scull em competição 2.07 (12.0 MET, 18080); single scull em ritmo de prova 2.67 (15.5 MET, 18114) |
| Canoagem, ritmo moderado | 18050 | 5.8 | 406 | direto | caiaque moderado 0.86 (5.0 MET, 18100); competição 2.16 (12.5 MET, 18060) |
| Triatlo | — | — | — | sem MET único | usar a etapa: natação 8.0, ciclismo 7.0, corrida de triatlo 13.0 (12620) |
| Voleibol, jogo em quadra | 15711 | 6.0 | 420 | direto | recreativo 0.50 (3.0 MET, 15720) |
| Tênis | 15675 | 6.8 | 476 | direto | duplas 0.66 (4.5 MET, 15685); simples 1.18 (8.0 MET, 15690) |
| Tênis de mesa | 15660 | 4.0 | 280 | direto | código único |
| Vôlei de praia | 15725 | 8.0 | 560 | direto | código único |
| Badminton, social | 15030 | 5.5 | 385 | direto | competitivo 1.27 (7.0 MET, 15020); partida 1.64 (9.0 MET, 15025) |
| Squash | 15652 | 7.3 | 511 | direto | código histórico de jogo 1.64 (12.0 MET, 15650) |
| Padel | 15680 | 6.0 | 420 | proxy | não há código; proxy é tênis em duplas. Não é medição de padel |
| Futevôlei | 15725 | 8.0 | 560 | proxy | não há código; proxy é vôlei de praia. Não é medição de futevôlei |
| Judô | 15433 | 11.3 | 791 | direto | prática iniciante do grupo de lutas 0.47 (5.3 MET, 15425) |
| Boxe, sparring | 15120 | 7.8 | 546 | direto | saco 0.74 (5.8 MET, 15110); no ringue 1.58 (12.3 MET, 15100). O 12.3 vale para o round, não para a hora inteira de aula |
| Karatê | 15430 | 10.3 | 721 | direto | o código cita karatê em ritmo moderado; iniciante 0.51 (5.3 MET, 15425) |
| Taekwondo | 15430 | 10.3 | 721 | direto | simulação de combate 1.39 (14.3 MET, 15432) |
| Jiu-jitsu | 15430 | 10.3 | 721 | direto | o código cita jujitsu em ritmo moderado; iniciante 0.51 (5.3 MET, 15425) |
| Muay Thai | 15430 | 10.3 | 721 | direto | o código cita Muay Thai em ritmo moderado; kickboxing isolado é outro código, 0.71 (7.3 MET, 15457) |
| MMA | 15430 | 10.3 | 721 | correspondência | sem código de MMA; mesma base das lutas moderadas do código 15430 |
| Esgrima | 15200 | 6.0 | 420 | direto | espada competitiva 1.63 (9.8 MET, 15203) |
| Luta olímpica | 15730 | 6.0 | 420 | direto | o código descreve a luta (um combate ≈ 5 min). Em treino com pausa longa, conte só os minutos de esforço |
| Golfe | 15255 | 4.5 | 315 | direto | com carrinho 0.78 (3.5 MET, 15290) |
| Tiro com arco | 15010 | 4.3 | 301 | direto | código único |
| Boliche | 15092 | 3.8 | 266 | direto | código único da pista |
| Sinuca / bilhar | 15080 | 2.5 | 175 | direto | código único |
| Tiro esportivo, em pé | 04130 | 2.5 | 175 | direto | pistola ou fossa olímpica; rifle ajoelhado ou em pé é o mesmo 2.5 MET (04145) |
| Ginástica artística | 15300 | 3.8 | 266 | direto | código geral de ginástica |
| Ginástica rítmica | 15300 | 3.8 | 266 | proxy | não há código; proxy é ginástica geral. Não é medição de rítmica |
| Surfe | 18220 | 3.0 | 210 | direto | inclui espera na prancha. Competitivo 1.67 (5.0 MET, 18222); só a remada 2.27 (6.8 MET, 18221) |
| Saltos ornamentais | 18090 | 3.0 | 210 | direto | trampolim ou plataforma |
| Skate | 15580 | 5.0 | 350 | direto | competitivo 1.20 (6.0 MET, 15582) |
| Nado artístico | 18330 | 8.0 | 560 | direto | código único |
| Esqui alpino | 19160 | 6.3 | 441 | direto | só tempo ativo, sem fila nem cadeira. Leve 0.68 (4.3 MET, 19150); vigoroso 1.27 (8.0 MET, 19170) |
| Snowboard | 19201 | 7.5 | 525 | direto | recreativo moderado na montanha. O par leve/vigoroso do esqui alpino também cobre snowboard (4.3 e 8.0 MET) |
| Hipismo | 15370 | 5.5 | 385 | direto | passo 0.69 (3.8 MET, 15400); galope 1.33 (7.3 MET, 15395) |
| Beisebol | 15620 | 5.0 | 350 | direto | código único de jogo moderado; inclui softball |
| Críquete | 15150 | 4.8 | 336 | direto | rebater, arremessar e defender |

Remo na água e canoagem compartilham o código moderado 18050. A diferença publicada aparece na competição: crew 12.0 MET, canoagem/caiaque em prova 12.5 MET. Sessão na máquina de remo usa a base de cardio (7.3), não esta.

## Força e hipertrofia

Fonte das modalidades: [strong](strong.md). O custo de uma hora de força é baixo porque a série dura segundos e o resto da hora é pausa. O MET já é a média da hora, não o pico da repetição.

| Modalidade | Código | kcal/kg em 60 min | 70 kg, 60 min | Vínculo | Intensidade |
| --- | ---: | ---: | ---: | --- | --- |
| Musculação tradicional (vários exercícios, 8–15 repetições) | 02054 | 3.5 | 245 | direto | circuito em supersérie 1.66 (5.8 MET, 02055); potência ou fisiculturismo vigoroso 1.71 (6.0 MET, 02050) |
| Calistenia de força (flexão, barra, abdominal, afundo) | 02022 | 3.8 | 266 | direto | alta intensidade 1.71 (6.5 MET, 02057). Burpee e polichinelo em circuito usam a base de cardio (6.0), não esta |
| Powerlifting | 02050 | 6.0 | 420 | direto | agachamento e terra, lento ou explosivo 0.83 (5.0 MET, 02052) |
| LPO | 02052 | 5.0 | 350 | correspondência | não há código de arranco e arremesso; mesma base do halterofilismo. Potência vigorosa 1.20 (6.0 MET, 02050) |
| Strongman | 02050 | 6.0 | 420 | proxy | não há código; proxy é levantamento vigoroso de potência ou fisiculturismo. Não é medição de strongman |
| CrossFit / cross training | 02040 | 7.5 | 525 | correspondência | circuito com kettlebell, trecho aeróbico e pausa curta. Não é um estudo de CrossFit. HIIT do código 02214 é a âncora 1.47 (11.0 MET) |
| Preparação física esportiva | 02062 | 7.8 | 546 | correspondência | aula de condicionamento. Não há código por esporte |

## O que não fazer com a base

- Não multiplicar a coluna de 70 kg pelo peso do cliente. Essa coluna já tem o peso dentro. A conta parte de `kcal/kg em 60 min`.
- Não aplicar fator de intensidade e, ao mesmo tempo, trocar a base por outro código. Uma das duas coisas.
- Não somar o bruto em cima de um dia que já foi multiplicado por nível de atividade. Nesse caso entra o líquido `(base - 1)`.
- Não preencher padel, futevôlei, ginástica rítmica, strongman ou triatlo com um MET único “estimado” fora das linhas acima.

## Fontes

- Ainsworth BE, Herrmann SD, Jacobs DR Jr, Whitt-Glover MC, Tudor-Locke C. 2024 Adult Compendium of Physical Activities: a third update of the energy costs of human activities. Os códigos e os MET das tabelas acima saem desse compêndio, na edição para adultos de 19 a 59 anos, publicada em [pacompendium.com](https://pacompendium.com/).
- 1 MET = 1 kcal por kg em 60 minutos é a convenção do próprio compêndio (1 MET ≈ 3,5 mL de oxigênio por kg por minuto).
