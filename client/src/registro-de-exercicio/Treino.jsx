import { useEffect, useState } from 'react'
import { mensagem } from '../acesso/mensagem.js'
import { useFormularioAcesso } from '../acesso/formulario.js'
import Voltar from '../components/Voltar.jsx'
import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import Input from '../components/ui/input.jsx'
import Label from '../components/ui/label.jsx'
import { guardarSessao, lerModalidades, lerSessoes } from './api.js'
import { esquemaSessao } from './esquema.js'

function Gasto({ valor }) {
  return (
    <Card className="flex flex-col gap-2">
      <p className="text-sm text-zinc-400">Gasto líquido da sessão</p>
      <p className="flex items-baseline gap-2">
        <span className="text-4xl font-semibold tracking-tight text-emerald-500 tabular-nums">{valor}</span>
        <span className="text-sm text-zinc-400">kcal</span>
      </p>
    </Card>
  )
}

function Formulario({ modalidades, onGuardou }) {
  const { registrar, aoEnviar, erros, aviso } = useFormularioAcesso(esquemaSessao, {
    modality_code: modalidades[0]?.code ?? '',
    duration_min: '',
    intensity_code: 'padrao',
    already_in_lifestyle: false,
  })
  const modalidade = registrar('modality_code')
  const minutos = registrar('duration_min')
  const intensidade = registrar('intensity_code')
  const inclusa = registrar('already_in_lifestyle')
  const [codigo, setCodigo] = useState(modalidades[0]?.code ?? '')
  const escolhida = modalidades.find((item) => item.code === codigo) ?? modalidades[0]

  return (
    <form
      aria-label="Treino"
      className="flex flex-col gap-4"
      noValidate
      onSubmit={aoEnviar(async (dados) => {
        const gravada = await guardarSessao({
          ...dados,
          duration_min: Number(dados.duration_min),
        })
        onGuardou(gravada)
      })}
    >
      <Card className="flex flex-col gap-3">
        <div className="flex flex-col gap-2">
          <Label htmlFor="modality_code">Modalidade</Label>
          <select
            id="modality_code"
            className="h-11 w-full rounded-md border border-zinc-800 bg-zinc-950 px-3 text-base text-zinc-50 focus:bg-zinc-800 focus-visible:ring-2 focus-visible:ring-emerald-500"
            {...modalidade}
            onChange={(evento) => {
              setCodigo(evento.target.value)
              modalidade.onChange(evento)
            }}
          >
            {modalidades.map((item) => (
              <option key={item.code} value={item.code}>{item.name}</option>
            ))}
          </select>
          {erros.modality_code?.message ? <p className="text-sm text-red-500" role="alert">{erros.modality_code.message}</p> : null}
        </div>
        <div className="flex flex-col gap-2">
          <Label htmlFor="intensity_code">Intensidade</Label>
          <select
            id="intensity_code"
            className="h-11 w-full rounded-md border border-zinc-800 bg-zinc-950 px-3 text-base text-zinc-50 focus:bg-zinc-800 focus-visible:ring-2 focus-visible:ring-emerald-500"
            {...intensidade}
          >
            {(escolhida?.intensities ?? []).map((item) => (
              <option key={item.code} value={item.code}>{item.name}</option>
            ))}
          </select>
          {erros.intensity_code?.message ? <p className="text-sm text-red-500" role="alert">{erros.intensity_code.message}</p> : null}
        </div>
        <div className="flex flex-col gap-2">
          <Label htmlFor="duration_min">Minutos</Label>
          <Input id="duration_min" inputMode="numeric" {...minutos} />
          {erros.duration_min?.message ? <p className="text-sm text-red-500" role="alert">{erros.duration_min.message}</p> : null}
        </div>
        <label className="flex min-h-11 items-center gap-3 text-sm" htmlFor="already_in_lifestyle">
          <input id="already_in_lifestyle" type="checkbox" className="size-5 accent-emerald-500" {...inclusa} />
          Essa hora já faz parte da rotina
        </label>
      </Card>
      {aviso ? <p className="text-sm text-red-500" role="alert">{aviso}</p> : null}
      <Button type="submit">Registrar</Button>
    </form>
  )
}

export default function Treino() {
  const [modalidades, setModalidades] = useState(null)
  const [sessoes, setSessoes] = useState([])
  const [ultima, setUltima] = useState(null)
  const [falha, setFalha] = useState('')

  useEffect(() => {
    let ativa = true

    Promise.all([lerModalidades(), lerSessoes()])
      .then(([lista, dia]) => {
        if (ativa) {
          setModalidades(lista)
          setSessoes(dia)
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

  function aoGuardar(sessao) {
    setUltima(sessao)
    setSessoes((atual) => [...atual, sessao])
  }

  return (
    <div className="flex flex-col gap-4">
      <Voltar />
      <div className="flex flex-col gap-1">
        <h2 className="text-2xl font-semibold tracking-tight">Treino</h2>
        <p className="text-sm text-zinc-400">Cardio ou esporte entra na manutenção.</p>
      </div>
      {falha ? <p className="text-sm text-red-500" role="alert">{falha}</p> : null}
      {ultima ? <Gasto valor={ultima.net_kcal} /> : null}
      {modalidades ? <Formulario modalidades={modalidades} onGuardou={aoGuardar} /> : null}
      {sessoes.length ? (
        <Card>
          <ul className="flex flex-col gap-2">
            {sessoes.map((sessao) => (
              <li key={sessao.id} className="flex items-baseline justify-between gap-3 text-sm">
                <span>{sessao.modality_name}</span>
                <span className="tabular-nums text-zinc-400">{sessao.net_kcal} kcal</span>
              </li>
            ))}
          </ul>
        </Card>
      ) : null}
    </div>
  )
}
