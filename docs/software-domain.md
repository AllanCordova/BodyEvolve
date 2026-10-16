# BodyEvolve

## 1. O Problema
Transformar o corpo e o condicionamento físico do usuário em direção a um objetivo, garantindo que as ações diárias (alimentação e exercícios) estejam alinhadas com a meta desejada.

## 2. Objetivos de Transformação (As Mudanças)
O usuário seleciona uma meta. O cálculo dessa meta está em [nutritional-plan](domain/nutritional-plan.md).

*   **Ganho de Massa Muscular (Hipertrofia)**
*   **Perda de Peso (Emagrecimento)**
*   **Ganho de Força**
*   **Ganho de Resistência (Condicionamento Cardiovascular)**

## 3. Entidades Principais (O que o usuário informa)

### 3.1. Perfil Biológico (Antropometria)
*   Idade
*   Peso
*   Sexo biológico
*   Altura

### 3.2. Registro de Atividades Físicas (O Estímulo)
Fonte de dados baseada em tempo de execução, intensidade e tipo de exercício. Nas metas de força e de hipertrofia a sessão também traz exercício, carga e repetições. A regra de progressão está em [stimulus](domain/stimulus.md).
*   **Treinos de Força/Hipertrofia:** [strong](domain/strong.md)
*   **Treinos Cardiovasculares:** [cardio](domain/cardio.md)
*   **Esportes:** [sports](domain/sports.md)
*   **Estilo de vida:** o quanto a rotina do dia é ativa, fora do treino. Entra como peso sobre o basal, em [nutritional-plan](domain/nutritional-plan.md).

### 3.3. Registro Nutricional (O Combustível)
Políticas de cálculo, metas e distribuição de nutrientes: [nutritional-plan](domain/nutritional-plan.md).
*   **Macronutrientes:** Carboidratos, Proteínas, Gorduras.
*   **Micronutrientes e Fibras.**
*   *Cálculo de ingestão calórica total.*

## 4. Motor de Validação e Cruzamento de Dados (Core Business)
A plataforma analisa a **combinação entre o balanço energético e o estímulo físico** para determinar se o usuário está no caminho certo.

### 4.1. Regras de Validação Cruzada
*   **Validação Termodinâmica e Nutricional:** O consumo de calorias e a distribuição de macronutrientes (ex: ingestão de proteínas) condizem com a meta?
*   **Validação de Estímulo de Treino:** O tipo de atividade física e a progressão de carga estão adequados para a meta selecionada? A regra está em [stimulus](domain/stimulus.md).
    *   *Regra de Negócio:* **Ganho de Força** e **Ganho de Massa** exigem sessão do catálogo de força. Comer mais, ou registrar só cardio, não cumpre a meta. A meta só está caminhando quando a carga do exercício multiarticular sobe de uma exposição para a outra, dentro da faixa de repetições da meta.

### 4.2. Sistema de Alerta e Correção de Rota (Exemplos Práticos)
*   **Cenário de Erro Calórico:** "Quero hipertrofia, mas estou em déficit." -> *Alerta do App: Ajustar a ingestão. Essa meta pede 15% acima da manutenção.*
*   **Cenário de Erro de Estímulo (Falta de Treino Adequado):** "Quero ganhar força. Minha dieta está na manutenção, mas meus únicos registros são caminhadas." -> *Alerta do App: Falta o estímulo. Força exige sessão do catálogo de força, com carga e repetições.*
*   **Cenário de Estagnação:** "Faço musculação duas vezes por semana, a dieta está certa, e o agachamento repetiu a mesma carga e as mesmas repetições nas duas últimas sessões." -> *Alerta do App: A meta não está avançando nesse exercício. Sem repetições extras nem carga maior, não houve progressão.*
