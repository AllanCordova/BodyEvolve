import { useEffect, useState } from 'react'
import { mensagem } from '../acesso/mensagem.js'
import { useFormularioAcesso } from '../acesso/formulario.js'
import Voltar from '../components/Voltar.jsx'
import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import Input from '../components/ui/input.jsx'
import Label from '../components/ui/label.jsx'
import { guardarSessao, lerExercicios, lerModalidades, lerSessoes } from './api.js'
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

function Formulario({ modalidades, exercicios, onGuardou }) {
  const { registrar, aoEnviar, erros, aviso } = useFormularioAcesso(esquemaSessao, {
    modality_code: modalidades[0]?.code ?? '',
    duration_min: '',
    intensity_code: 'padrao',
    already_in_lifestyle: false,
    exercise_code: exercicios[0]?.code ?? '',
    sets_done: '',
    reps_done: '',
    load_kg: '',
  })
  const modalidade = registrar('modality_code')
  const minutos = registrar('duration_min')
  const intensidade = registrar('intensity_code')
  const inclusa = registrar('already_in_lifestyle')
  const exercicio = registrar('exercise_code')
  const series = registrar('sets_done')
  const repeticoes = registrar('reps_done')
  const carga = registrar('load_kg')
  const [codigo, setCodigo] = useState(modalidades[0]?.code ?? '')
  const [codigoExercicio, setCodigoExercicio] = useState(exercicios[0]?.code ?? '')
  const escolhida = modalidades.find((item) => item.code === codigo) ?? modalidades[0]
  const escolhido = exercicios.find((item) => item.code === codigoExercicio)
  const forca = Boolean(escolhida?.counts_as_strength)

  return (
    <form
      aria-label="Treino"
      className="flex flex-col gap-4"
      noValidate
      onSubmit={aoEnviar(async (dados) => {
        const pedido = {
          modality_code: dados.modality_code,
          duration_min: Number(dados.duration_min),
          intensity_code: dados.intensity_code,
          already_in_lifestyle: dados.already_in_lifestyle,
        }

        if (forca) {
          pedido.exercises = [{
            exercise_code: dados.exercise_code,
            sets_done: Number(dados.sets_done),
            reps_done: Number(dados.reps_done),
            load_kg: Number(dados.load_kg),
          }]
        }

        onGuardou(await guardarSessao(pedido))
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
        {forca ? (
          <>
            {escolhido?.rep_min !== undefined ? (
              <p className="text-sm text-zinc-400">
                Faixa <span className="tabular-nums text-zinc-50">{escolhido.rep_min}</span>
                {' a '}
                <span className="tabular-nums text-zinc-50">{escolhido.rep_max}</span>
              </p>
            ) : null}
            <div className="flex flex-col gap-2">
              <Label htmlFor="exercise_code">Exercício</Label>
              <select
                id="exercise_code"
                className="h-11 w-full rounded-md border border-zinc-800 bg-zinc-950 px-3 text-base text-zinc-50 focus:bg-zinc-800 focus-visible:ring-2 focus-visible:ring-emerald-500"
                {...exercicio}
                onChange={(evento) => {
                  setCodigoExercicio(evento.target.value)
                  exercicio.onChange(evento)
                }}
              >
                {exercicios.map((item) => (
                  <option key={item.code} value={item.code}>{item.name}</option>
                ))}
              </select>
            </div>
            <div className="flex flex-col gap-2">
              <Label htmlFor="sets_done">Séries</Label>
              <Input id="sets_done" inputMode="numeric" {...series} />
            </div>
            <div className="flex flex-col gap-2">
              <Label htmlFor="reps_done">Repetições</Label>
              <Input id="reps_done" inputMode="numeric" {...repeticoes} />
            </div>
            <div className="flex flex-col gap-2">
              <Label htmlFor="load_kg">Carga (kg)</Label>
              <Input id="load_kg" inputMode="decimal" {...carga} />
            </div>
          </>
        ) : null}
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
  const [exercicios, setExercicios] = useState([])
  const [sessoes, setSessoes] = useState([])
  const [ultima, setUltima] = useState(null)
  const [falha, setFalha] = useState('')

  useEffect(() => {
    let ativa = true

    Promise.all([lerModalidades(), lerSessoes(), lerExercicios()])
      .then(([lista, dia, catalogo]) => {
        if (ativa) {
          setModalidades(lista)
          setSessoes(dia)
          setExercicios(catalogo)
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
      {ultima?.rep_min !== undefined ? (
        <p className="text-sm text-zinc-400">
          Faixa da meta <span className="tabular-nums text-zinc-50">{ultima.rep_min}</span>
          {' a '}
          <span className="tabular-nums text-zinc-50">{ultima.rep_max}</span>
        </p>
      ) : null}
      {modalidades ? <Formulario modalidades={modalidades} exercicios={exercicios} onGuardou={aoGuardar} /> : null}
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
