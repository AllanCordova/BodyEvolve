# Plano Nutricional

## Perfil e Jornada do Cliente
Para que o produto entregue valor, precisamos entender o momento atual do cliente. A jornada de *onboarding* deve capturar obrigatoriamente:
* **Métricas Biológicas:** Idade, Gênero, Peso e Altura.
* **Estilo de Vida:** O quanto a rotina do dia é ativa, fora do treino lançado. É um peso, não uma sessão.
* **Rotina de exercícios:** Quais modalidades o cliente pratica, com o gasto em [consumption](consumption.md).
* **Meta:** Uma entre Hipertrofia, Emagrecimento, Força e Resistência.

## Políticas de Cálculo Base
Antes de adaptar a dieta para um objetivo específico, o produto precisa descobrir qual é a "estaca zero" do cliente. A manutenção junta dois inputs: o peso da rotina diária e as sessões de treino. O basal vem do perfil.

* **Custo energético básico.** Equação de Mifflin-St Jeor (*Am J Clin Nutr.* 1990;51(2):241-247), em kcal/dia, com o sexo biológico do perfil:
  * Masculino: `(10 × peso_kg) + (6.25 × altura_cm) − (5 × idade) + 5`
  * Feminino: `(10 × peso_kg) + (6.25 × altura_cm) − (5 × idade) − 161`
* **Estilo de vida.** Peso sobre o basal. Descreve trabalho, casa e deslocamento do dia. Não é exercício e não é contagem de passos. A fonte é o relatório conjunto FAO/OMS/UNU de 2001 (*Human energy requirements*), Tabela 5.1, que calcula o dia inteiro como múltiplo do basal:

| Rotina | Peso | O que a tabela descreve |
| --- | ---: | --- |
| Sedentária ou leve | 1.53 | Escritório, casa leve, pouco andar, lazer sentado. Sem esporte no dia. |
| Ativa ou moderada | 1.76 | Em pé, carga leve, andar no dia, mais cerca de uma hora de movimento aeróbico leve. |
| Vigorosa | 2.25 | Trabalho braçal por várias horas, ou lazer intenso por várias horas. |

A faixa oficial de cada rotina, na Tabela 5.3 do mesmo relatório, é 1.40–1.69, 1.70–1.99 e 2.00–2.40. O produto usa o valor fechado da Tabela 5.1, não a faixa.

* **Rotina de exercícios.** Soma das kcal líquidas das sessões em [consumption](consumption.md): `(base − 1) × peso_kg × (minutos / 60) × fator_intensidade`. O `(base − 1)` tira o repouso que o basal já cobriu.
* **Manutenção.**

```text
manutenção = (basal × peso_da_rotina) + kcal_líquidas_das_sessões
```

O peso da FAO já inclui o efeito térmico da comida e o movimento habitual daquele estilo de vida. A sessão lançada soma por cima. A hora de treino que já está dentro do peso escolhido não entra de novo: a rotina moderada da Tabela 5.1 já contém uma hora de aeróbico leve. Essa mesma hora, se também foi lançada em [consumption](consumption.md), conta uma vez só.

Exemplo: basal de 1700 kcal e rotina sedentária. `1700 × 1.53 = 2601`. Uma sessão extra, ainda não representada nesse peso, soma a kcal líquida dela em cima dos 2601. 

## Estratégias e Regras de Negócio por Objetivo
A partir do Custo Energético Total, o produto aplica nossas políticas de nutrição de acordo com a escolha do cliente.

### Cliente focado em Ganho de Massa Muscular (Hipertrofia)
* **Diretriz Calórica:** O sistema deve gerar um ambiente de abundância controlada (superávit). A recomendação padrão adiciona 15% a mais de calorias ao gasto diário do cliente.
* **Distribuição de Nutrientes:**
  * **Proteína (O Construtor):** Fixar em 2 gramas por quilo de peso do cliente para maximizar a construção muscular.
  * **Gordura (Saúde Hormonal):** Fixar em 1 grama por quilo de peso.
  * **Carboidrato (A Energia):** Receberá todo o saldo restante das calorias liberadas para o dia, garantindo energia para treinos intensos.

### Cliente focado em Perda de Peso (Emagrecimento)
* **Diretriz Calórica:** O sistema deve forçar o corpo a usar suas reservas, criando um déficit. A recomendação padrão reduz 20% das calorias do gasto diário.
* **Distribuição de Nutrientes:**
  * **Proteína (Proteção):** Aumentar para 2.2 gramas por quilo. A regra de negócio aqui é proteger a massa muscular do cliente enquanto ele perde gordura.
  * **Gordura (Limite de Segurança):** Fixar em 0.8 gramas por quilo. *Regra Crítica de Segurança:* O produto nunca recomendará menos de 0.6g/kg para evitar danos à saúde hormonal do cliente.
  * **Carboidrato:** Receberá apenas o saldo de calorias que sobrar, mantendo a dieta mais restrita.

### Cliente focado em Ganho de Força (Powerlifting/LPO)
* **Diretriz Calórica:** O foco não é mudar a estética, mas a performance. O sistema manterá as calorias no nível de Manutenção ou oferecerá um ganho muito leve (5% a mais).
* **Distribuição de Nutrientes:**
  * **Proteína:** Manter em 1.8 gramas por quilo, suficiente para recuperação de tendões e músculos.
  * **Gordura:** Fixar em 1.1 gramas por quilo.
  * **Carboidrato:** Estabilizar em cerca de 4 gramas por quilo para garantir recuperação do sistema nervoso entre sessões pesadas. Se esses 4 g/kg não couberem no saldo depois da proteína e da gordura, o carboidrato fica no que sobrar e o sistema alerta. O saldo não sobe para caber o carboidrato.

### Cliente focado em Resistência (Condicionamento/Endurance)
* **Diretriz Calórica:** As sessões entram na manutenção pela soma acima. A meta é repor esse gasto: saldo de 0% sobre a manutenção.
* **Distribuição de Nutrientes:**
  * **Proteína:** Manter uma base moderada de 1.4 gramas por quilo.
  * **Gordura:** Fixar em 1.2 gramas por quilo.
  * **Carboidrato (Combustível Primário):** Prioridade total do algoritmo. O sistema tentará entregar 8 gramas por quilo. *Regra de Experiência:* Se o perfil do cliente não permitir atingir o mínimo necessário de carboidratos, o produto deve alertar que a rotina de treinos exige mais alimentação.

## Micronutrientes

* **Ganho de Massa Muscular (Hipertrofia):**
  * **Foco:** Zinco, Magnésio e Vitamina D3.
  * **Justificativa de Sistema:** Esses nutrientes são essenciais para a síntese proteica, recuperação e suporte à testosterona. O sistema deve priorizar alimentos ou alertas que contenham estes itens.

* **Perda de Peso (Emagrecimento):**
  * **Foco:** Cálcio, Ferro e Complexo B.
  * **Justificativa de Sistema:** Necessários para a manutenção da densidade óssea, suporte à tireoide e otimização do metabolismo energético durante o período de restrição e déficit calórico.

* **Ganho de Força:**
  * **Foco:** Cálcio, Vitamina D3 e Magnésio.
  * **Justificativa de Sistema:** O sistema deve alertar sobre estes micronutrientes com foco no fortalecimento da densidade óssea para suportar cargas máximas e na recuperação do Sistema Nervoso Central.

* **Resistência (Condicionamento/Endurance):**
  * **Foco:** Eletrólitos (Sódio, Potássio, Magnésio) e Ferro.
  * **Justificativa de Sistema:** O sistema deve gerar alertas ativos de hidratação e reposição, pois são vitais para a reposição de sais perdidos no suor e otimização do transporte de oxigênio pela hemoglobina/mioglobina.

## Fontes

- Mifflin MD, St Jeor ST, Hill LA, Scott BJ, Daugherty SA, Koh YO. A new predictive equation for resting energy expenditure in healthy individuals. *Am J Clin Nutr.* 1990;51(2):241-247. É a equação do custo energético básico.
- FAO, WHO, UNU. *Human energy requirements.* Report of a Joint FAO/WHO/UNU Expert Consultation, Rome, 17–24 October 2001. FAO Food and Nutrition Technical Report Series 1, 2004. A Tabela 5.1 fecha os pesos 1.53, 1.76 e 2.25. A Tabela 5.3 publica as faixas 1.40–1.69, 1.70–1.99 e 2.00–2.40.