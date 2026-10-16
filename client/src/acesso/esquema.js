import { z } from 'zod'

const email = z.string().trim().min(1, 'Informe o e-mail.').email('Informe um e-mail válido.')

export const esquemaEntrar = z.object({
  email,
  password: z.string().min(1, 'Informe a senha.'),
})

export const esquemaConta = z.object({
  name: z.string().trim().min(1, 'Informe o nome.'),
  email,
  password: z.string().min(1, 'Informe a senha.').min(8, 'A senha precisa ter pelo menos 8 caracteres.'),
})
