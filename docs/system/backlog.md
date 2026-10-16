# Backlog

Funções do produto, uma meta por pessoa. A regra continua em [software-domain](../software-domain.md), [nutritional-plan](../domain/nutritional-plan.md), [consumption](../domain/consumption.md) e [stimulus](../domain/stimulus.md).

## Entrada

### US-01 Entrar na plataforma
Como pessoa dona dos próprios dados, quero entrar com e-mail e senha, para ver só o meu plano, o meu treino e a minha comida.

- Crio a conta com nome, e-mail e senha.
- Entro com esses dados.
- Sair encerra o acesso.
- Sem sessão, perfil, treino, comida e alertas não abrem.
- O que eu lanço fica na conta com a qual entrei.

## Perfil

### US-02 Informar o corpo
Como pessoa nova no BodyEvolve, quero guardar idade, sexo biológico, peso e altura, para o sistema calcular meu gasto em repouso.

- Sexo biológico é masculino ou feminino.
- Peso entra em quilos e altura em centímetros.
- Sem esses quatro dados, manutenção e meta não são calculadas.
- Se eu atualizar o peso, os cálculos seguintes usam o peso novo.

### US-03 Escolher uma meta
Como pessoa com um objetivo, quero marcar uma meta entre hipertrofia, emagrecimento, força e resistência, para o plano do dia seguir uma regra só.

- A tela oferece as quatro metas e aceita uma.
- Trocar a meta recalcula o alvo de calorias e de nutrientes.
- Não existe combinação de duas metas.

### US-04 Dizer o quanto o dia é ativo
Como pessoa que trabalha e se desloca fora do treino, quero escolher sedentária, moderada ou vigorosa, para esse hábito pesar no meu gasto do dia.

- Sedentária multiplica o basal por 1.53.
- Moderada multiplica por 1.76.
- Vigorosa multiplica por 2.25.
- Essa escolha não é uma sessão de exercício.

## Plano do dia

### US-05 Ver a manutenção
Como pessoa com perfil preenchido, quero ver quantas calorias mantenho o peso, para saber a linha de base antes da meta.

- O basal usa Mifflin-St Jeor com sexo, peso, altura e idade.
- A manutenção é `(basal × peso da rotina) + kcal líquida das sessões do dia`.
- Sessão marcada como já inclusa na rotina não entra de novo na soma.
- Sem sessão no dia, a manutenção é só `basal × peso da rotina`.

### US-06 Ver o alvo da meta
Como pessoa com uma meta escolhida, quero ver calorias, proteína, gordura e carboidrato do dia, para saber o que comer.

- Hipertrofia: manutenção + 15%. Proteína 2 g/kg. Gordura 1 g/kg. Carboidrato fica com as calorias que sobram.
- Emagrecimento: manutenção − 20%. Proteína 2.2 g/kg. Gordura 0.8 g/kg, nunca abaixo de 0.6 g/kg. Carboidrato fica com o que sobra.
- Força: a tela mostra a manutenção e também a manutenção + 5%. Proteína 1.8 g/kg. Gordura 1.1 g/kg. Carboidrato tenta 4 g/kg. Se não couber, fica o que sobra e aparece um aviso.
- Resistência: manutenção, sem acréscimo nem corte. Proteína 1.4 g/kg. Gordura 1.2 g/kg. Carboidrato tenta 8 g/kg. Se não couber, fica o que sobra e aparece um aviso.
- Proteína e carboidrato valem 4 kcal por grama. Gordura vale 9 kcal por grama.

### US-07 Ver o foco de micronutrientes
Como pessoa na meta, quero ver quais micronutrientes o plano destaca, para saber o que observar na comida.

- Hipertrofia destaca zinco, magnésio e vitamina D3.
- Emagrecimento destaca cálcio, ferro e complexo B.
- Força destaca cálcio, vitamina D3 e magnésio.
- Resistência destaca sódio, potássio, magnésio e ferro, e lembra hidratação.
- Esta história não confere miligrama ingerido. Ainda não há tabela de alimentos.

## Treino

### US-08 Lançar cardio ou esporte
Como pessoa que treinou fora da musculação, quero registrar modalidade, minutos e intensidade, para o gasto entrar na manutenção.

- A modalidade vem de [cardio](../domain/cardio.md) ou de [sports](../domain/sports.md).
- A intensidade padrão usa o fator 1. As outras intensidades usam o fator publicado em [consumption](../domain/consumption.md).
- O gasto líquido da sessão é `(base − 1) × peso_kg × (minutos / 60) × fator`.
- Posso marcar que essa hora já faz parte da rotina escolhida. Aí ela não soma de novo.

### US-09 Lançar treino de força
Como pessoa em hipertrofia ou força, quero escolher o exercício e informar séries, repetições e carga, para o app me dizer a faixa e guardar o histórico.

- A modalidade vem de [strong](../domain/strong.md). Halterofilismo lançado em esportes conta como LPO.
- O exercício vem do catálogo, não de um texto livre.
- Cada lançamento guarda séries, repetições feitas e quilos, ou a variação da calistenia. O lançamento anterior permanece.
- O app mostra o mínimo e o máximo de repetições da meta, em [stimulus](../domain/stimulus.md).
- Burpee, polichinelo e mountain climber lançados como cardio não contam como este treino.
- A sessão também gasta calorias, pela mesma conta da US-08.

### US-10 Ver se a carga subiu
Como pessoa em hipertrofia ou força, quero ver se o exercício multiarticular progrediu desde a última vez, para saber se a meta está andando.

- A comparação é com a exposição anterior do mesmo exercício, na mesma faixa de repetições.
- Hipertrofia usa a faixa de 6 a 12. Força de quem ainda treina em 8 a 12 usa essa faixa. Força pesada usa 1 a 6.
- Progrediu: a carga subiu e as repetições continuam na faixa.
- A caminho: a carga é a mesma e as repetições subiram.
- Estagnado: duas vezes seguidas na mesma carga, sem repetição extra e sem variação mais difícil.
- A primeira vez de um exercício não gera alerta.
- Se eu atingir o máximo de repetições da faixa em duas sessões seguidas, na mesma carga, o app manda subir a carga: 10% no multiarticular grande e 2% no músculo pequeno.
- Se eu não completar o mínimo, o app manda reduzir a carga até o mínimo voltar.

## Comida e alertas

### US-11 Registrar a comida do dia
Como pessoa que comeu ao longo do dia, quero informar calorias, proteína, gordura, carboidrato e fibra, para comparar com o alvo.

- O registro é do dia, não de cada alimento.
- Fibra é guardada. Ainda não existe nota de corte para ela.

### US-12 Saber se o dia está na rota
Como pessoa com meta, comida e treino lançados, quero um aviso quando a conta ou o estímulo desviar, para corrigir antes de insistir na rota errada.

- Hipertrofia com ingestão abaixo da manutenção avisa para subir a comida. O alvo é +15%.
- Força ou hipertrofia sem nenhuma sessão do catálogo de força na semana avisa que caminhada e cardio não substituem esse treino.
- Força ou hipertrofia com todo exercício multiarticular repetido em estado estagnado avisa que a carga não avançou.
- Menos de duas sessões de força na semana avisa a frequência. Não reprova a meta na primeira sessão.
- Hipertrofia com menos de 10 séries na faixa de 8 a 12 na semana, no grupo do exercício escolhido, avisa volume baixo.
- Força e resistência avisam se o carboidrato pedido em g/kg não coube no saldo.
- Emagrecimento nunca mostra gordura abaixo de 0.6 g/kg.
