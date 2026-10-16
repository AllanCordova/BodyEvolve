import { z } from 'zod'

export const esquemaMeta = z.object({
  goal: z.enum(['hipertrofia', 'emagrecimento', 'forca', 'resistencia'], {
    error: 'Escolha uma meta.',
  }),
})
