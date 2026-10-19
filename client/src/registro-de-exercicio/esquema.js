import { z } from 'zod'

export const esquemaSessao = z.object({
  modality_code: z.string().min(1, 'Escolha a modalidade.'),
  duration_min: z.coerce.number({ error: 'Informe os minutos.' }).int('Informe os minutos.').positive('Informe os minutos.'),
  intensity_code: z.string().min(1, 'Escolha a intensidade.'),
  already_in_lifestyle: z.boolean(),
})
