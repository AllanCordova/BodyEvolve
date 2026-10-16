import { z } from 'zod'

const inteiro = (mensagem) => z.coerce.number({ error: mensagem }).int({ error: mensagem }).positive({ error: mensagem })

export const esquemaRotina = z.object({
  lifestyle: z.enum(['sedentaria', 'moderada', 'vigorosa'], {
    error: 'Escolha uma rotina.',
  }),
})

export const esquemaCorpo = z.object({
  age_years: inteiro('Informe a idade.'),
  sex: z.enum(['masculino', 'feminino'], { error: 'O sexo biológico é masculino ou feminino.' }),
  weight_kg: z.coerce.number({ error: 'Informe o peso.' }).positive({ error: 'Informe o peso.' }),
  height_cm: inteiro('Informe a altura.'),
})
