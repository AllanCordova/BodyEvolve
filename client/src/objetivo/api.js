import { pedir, prepararCsrf } from '../acesso/api.js'

export function lerMeta() {
  return pedir('/api/objetivo')
}

export async function guardarMeta(dados) {
  await prepararCsrf()

  return pedir('/api/objetivo', {
    method: 'PUT',
    body: JSON.stringify(dados),
  })
}
