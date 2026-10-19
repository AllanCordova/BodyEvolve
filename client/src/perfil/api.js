import { pedir, prepararCsrf } from '../acesso/api.js'

export function lerCorpo() {
  return pedir('/api/perfil')
}

export function lerRotina() {
  return pedir('/api/perfil/rotina')
}

export async function guardarRotina(dados) {
  await prepararCsrf()

  return pedir('/api/perfil/rotina', {
    method: 'PUT',
    body: JSON.stringify(dados),
  })
}

export async function guardarCorpo(dados) {
  await prepararCsrf()

  return pedir('/api/perfil', {
    method: 'PUT',
    body: JSON.stringify(dados),
  })
}
