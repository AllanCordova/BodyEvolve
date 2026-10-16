import { useEffect, useState } from 'react'
import { Link } from 'react-router'
import { mensagem } from '../acesso/mensagem.js'
import { useFormularioAcesso } from '../acesso/formulario.js'
import Voltar from '../components/Voltar.jsx'
import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import Escolha from '../components/ui/escolha.jsx'
import { guardarMeta, lerMeta } from './api.js'
import { esquemaMeta } from './esquema.js'

const opcoes = [
  ['hipertrofia', 'Hipertrofia', 'Construir massa'],
  ['emagrecimento', 'Emagrecimento', 'Reduzir o peso'],
  ['forca', 'Força', 'Subir a carga'],
  ['resistencia', 'Resistência', 'Sustentar o esforço'],
]

const nomes = Object.fromEntries(opcoes.map(([valor, rotulo]) => [valor, rotulo]))

function fatia(valor, total) {
  if (!total) {
    return 0
  }

  return (Number(valor) / total) * 100
}

function Anel({ nome, kcal, protein_g, carb_g, fat_g }) {
  const total = Number(protein_g) + Number(carb_g) + Number(fat_g)
  const proteina = fatia(protein_g, total)
  const carboidrato = fatia(carb_g, total)
  const fundo = `conic-gradient(var(--color-pink-500) 0% ${proteina}%, var(--color-amber-500) ${proteina}% ${proteina + carboidrato}%, var(--color-violet-500) ${proteina + carboidrato}% 100%)`

  return (
    <Card className="flex flex-col items-center gap-5">
      <p className="text-sm text-zinc-400">{nome}</p>
      <div
        className="grid size-44 place-items-center rounded-full"
        style={{ background: fundo }}
      >
        <div className="grid size-32 place-items-center rounded-full bg-zinc-900">
          <p className="text-center">
            <span className="block text-3xl font-semibold tracking-tight text-emerald-500 tabular-nums">{kcal}</span>
            <span className="text-xs text-zinc-400">kcal</span>
          </p>
        </div>
      </div>
      <ul className="grid w-full grid-cols-3 gap-2">
        <Macro nome="Proteína" valor={protein_g} cor="text-pink-500" />
        <Macro nome="Carboidrato" valor={carb_g} cor="text-amber-500" />
        <Macro nome="Gordura" valor={fat_g} cor="text-violet-500" />
      </ul>
    </Card>
  )
}

function Macro({ nome, valor, cor }) {
  return (
    <li className="flex flex-col items-center gap-1 text-center">
      <span className={`text-xs ${cor}`}>{nome}</span>
      <span className="text-base font-medium tabular-nums">
        <span>{valor}</span>
        <span className="text-xs font-normal text-zinc-400"> g</span>
      </span>
    </li>
  )
}

function FormularioMeta({ inicial, onGuardou }) {
  const [alvo, setAlvo] = useState(inicial)
  const { registrar, aoEnviar, erros, aviso } = useFormularioAcesso(esquemaMeta, {
    goal: inicial.goal ?? '',
  })
  const meta = registrar('goal')
  const temAlvo = alvo.kcal !== undefined && alvo.kcal !== null

  return (
    <form
      aria-label="Meta"
      className="flex flex-col gap-4"
      noValidate
      onSubmit={aoEnviar(async (dados) => {
        const gravada = await guardarMeta(dados)
        setAlvo(gravada)
        onGuardou(gravada)
      })}
    >
      {temAlvo ? (
        <Anel
          nome={nomes[alvo.goal] ?? 'Alvo'}
          kcal={alvo.kcal}
          protein_g={alvo.protein_g}
          carb_g={alvo.carb_g}
          fat_g={alvo.fat_g}
        />
      ) : null}
      {alvo.goal && !temAlvo ? (
        <p className="text-sm text-zinc-400">
          O alvo aparece quando o corpo está preenchido.{' '}
          <Link to="/perfil" className="font-medium text-zinc-50">Abrir o corpo</Link>
        </p>
      ) : null}
      <Card>
        <fieldset className="flex flex-col gap-2">
          <legend className="mb-2 text-sm font-medium">Uma meta</legend>
          <div className="flex flex-col gap-2">
            {opcoes.map(([valor, rotulo, detalhe]) => (
              <Escolha key={valor} valor={valor} rotulo={rotulo} detalhe={detalhe} {...meta} />
            ))}
          </div>
          {erros.goal?.message ? <p className="text-sm text-red-500" role="alert">{erros.goal.message}</p> : null}
        </fieldset>
      </Card>
      {aviso ? <p className="text-sm text-red-500" role="alert">{aviso}</p> : null}
      <Button type="submit">Guardar</Button>
    </form>
  )
}

export default function Meta() {
  const [meta, setMeta] = useState(null)
  const [falha, setFalha] = useState('')

  useEffect(() => {
    let ativa = true

    lerMeta()
      .then((atual) => {
        if (ativa) {
          setMeta(atual)
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
        <h2 className="text-2xl font-semibold tracking-tight">Meta</h2>
        <p className="text-sm text-zinc-400">O plano do dia segue uma meta só.</p>
      </div>
      {falha ? <p className="text-sm text-red-500" role="alert">{falha}</p> : null}
      {meta ? <FormularioMeta inicial={meta} onGuardou={setMeta} /> : null}
    </div>
  )
}
