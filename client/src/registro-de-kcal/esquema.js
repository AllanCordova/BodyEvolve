import { z } from 'zod'

const gramas = (rotulo) => z.coerce.number({ error: rotulo }).min(0, rotulo)

export const esquemaComida = z.object({
  kcal: z.coerce.number({ error: 'Informe as calorias.' }).int('Informe as calorias.').min(0, 'Informe as calorias.'),
  protein_g: gramas('Informe a proteína.'),
  fat_g: gramas('Informe a gordura.'),
  carb_g: gramas('Informe o carboidrato.'),
  fiber_g: gramas('Informe a fibra.'),
})
