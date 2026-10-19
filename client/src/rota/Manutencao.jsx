import { useEffect, useState } from 'react'
import { Link } from 'react-router'
import { mensagem } from '../acesso/mensagem.js'
import Voltar from '../components/Voltar.jsx'
import Card from '../components/ui/card.jsx'
import { lerManutencao } from './api.js'

export default function Manutencao() {
  const [plano, setPlano] = useState(null)
  const [falha, setFalha] = useState('')

  useEffect(() => {
    let ativa = true

    lerManutencao()
      .then((atual) => {
        if (ativa) {
          setPlano(atual)
        }
      })
      .catch((erro) => {
        if (ativa) {
          setFalha(mensagem(erro))
        }
      })

    return () => {
      ativa = false
    }
  }, [])

  const temManutencao = plano?.maintenance_kcal !== undefined && plano?.maintenance_kcal !== null

  return (
    <div className="flex flex-col gap-4">
      <Voltar />
      <div className="flex flex-col gap-1">
        <h2 className="text-2xl font-semibold tracking-tight">Manutenção</h2>
        <p className="text-sm text-zinc-400">A linha de base antes da meta.</p>
      </div>
      {falha ? <p className="text-sm text-red-500" role="alert">{falha}</p> : null}
      {temManutencao ? (
        <Card className="flex flex-col gap-2">
          <p className="text-sm text-zinc-400">Calorias para manter o peso</p>
          <p className="flex items-baseline gap-2">
            <span className="text-4xl font-semibold tracking-tight text-emerald-500 tabular-nums">{plano.maintenance_kcal}</span>
            <span className="text-sm text-zinc-400">kcal</span>
          </p>
          {plano.basal_kcal !== null && plano.basal_kcal !== undefined ? (
            <p className="text-sm text-zinc-400">
              Basal <span className="tabular-nums text-zinc-50">{plano.basal_kcal}</span> kcal
            </p>
          ) : null}
        </Card>
      ) : null}
      {plano && !temManutencao ? (
        <p className="text-sm text-zinc-400">
          A manutenção aparece quando o corpo e a rotina estão preenchidos.{' '}
          <Link to="/perfil" className="font-medium text-zinc-50">Abrir o corpo</Link>
          {' · '}
          <Link to="/rotina" className="font-medium text-zinc-50">Abrir a rotina</Link>
        </p>
      ) : null}
    </div>
  )
}
