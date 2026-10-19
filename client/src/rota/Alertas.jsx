import { useEffect, useState } from 'react'
import { mensagem } from '../acesso/mensagem.js'
import Voltar from '../components/Voltar.jsx'
import Card from '../components/ui/card.jsx'
import { lerAlertas } from './api.js'

export default function Alertas() {
  const [alertas, setAlertas] = useState(null)
  const [falha, setFalha] = useState('')

  useEffect(() => {
    let ativa = true

    lerAlertas()
      .then((lista) => {
        if (ativa) {
          setAlertas(lista)
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

  return (
    <div className="flex flex-col gap-4">
      <Voltar />
      <div className="flex flex-col gap-1">
        <h2 className="text-2xl font-semibold tracking-tight">Alertas</h2>
        <p className="text-sm text-zinc-400">O aviso aparece quando a rota desvia.</p>
      </div>
      {falha ? <p className="text-sm text-red-500" role="alert">{falha}</p> : null}
      {alertas?.length === 0 ? <p className="text-sm text-zinc-400">Nenhum aviso hoje.</p> : null}
      {alertas?.map((alerta) => (
        <Card key={alerta.kind}>
          <p className={alerta.fails_goal ? 'text-sm text-yellow-500' : 'text-sm text-zinc-50'}>{alerta.message}</p>
        </Card>
      ))}
    </div>
  )
}
