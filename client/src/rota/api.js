import { pedir } from '../acesso/api.js'

export function lerManutencao() {
  return pedir('/api/manutencao')
}
