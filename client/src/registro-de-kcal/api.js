import { pedir, prepararCsrf } from '../acesso/api.js'

export function lerComida() {
  return pedir('/api/comida')
}

export async function guardarComida(dados) {
  await prepararCsrf()

  return pedir('/api/comida', {
    method: 'PUT',
    body: JSON.stringify(dados),
  })
}
