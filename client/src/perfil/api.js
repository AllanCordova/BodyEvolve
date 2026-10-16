import { pedir, prepararCsrf } from '../acesso/api.js'

export function lerCorpo() {
  return pedir('/api/perfil')
}

export async function guardarCorpo(dados) {
  await prepararCsrf()

  return pedir('/api/perfil', {
    method: 'PUT',
    body: JSON.stringify(dados),
  })
}
