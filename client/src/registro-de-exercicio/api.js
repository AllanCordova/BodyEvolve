import { pedir, prepararCsrf } from '../acesso/api.js'

export function lerModalidades() {
  return pedir('/api/treino/modalidades')
}

export function lerSessoes() {
  return pedir('/api/treino/sessoes')
}

export async function guardarSessao(dados) {
  await prepararCsrf()

  return pedir('/api/treino/sessoes', {
    method: 'POST',
    body: JSON.stringify(dados),
  })
}
