import { pedir, prepararCsrf } from '../acesso/api.js'

export function lerExercicios() {
  return pedir('/api/treino/exercicios')
}

export function lerModalidades() {
  return pedir('/api/treino/modalidades')
}

export function lerProgressao() {
  return pedir('/api/treino/progressao')
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
