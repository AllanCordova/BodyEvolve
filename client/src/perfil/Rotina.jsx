import { useEffect, useState } from 'react'
import { Link } from 'react-router'
import { mensagem } from '../acesso/mensagem.js'
import { useFormularioAcesso } from '../acesso/formulario.js'
import Voltar from '../components/Voltar.jsx'
import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import Escolha from '../components/ui/escolha.jsx'
import { guardarRotina, lerRotina } from './api.js'
import { esquemaRotina } from './esquema.js'

const opcoes = [
  ['sedentaria', 'Sedentária', 'Escritório, casa, pouco andar'],
  ['moderada', 'Moderada', 'Em pé e no deslocamento'],
  ['vigorosa', 'Vigorosa', 'Trabalho braçal ou lazer intenso'],
]

function Gasto({ valor }) {
  return (
    <Card className="flex flex-col gap-2">
      <p className="text-sm text-zinc-400">Gasto da rotina</p>
      <p className="flex items-baseline gap-2">
        <span className="text-4xl font-semibold tracking-tight text-emerald-500 tabular-nums">{valor}</span>
        <span className="text-sm text-zinc-400">kcal</span>
      </p>
    </Card>
  )
}

function FormularioRotina({ inicial, onGuardou }) {
  const [rotina, setRotina] = useState(inicial)
  const { registrar, aoEnviar, erros, aviso } = useFormularioAcesso(esquemaRotina, {
    lifestyle: inicial.lifestyle ?? '',
  })
  const escolha = registrar('lifestyle')
  const temGasto = rotina.gasto_da_rotina_kcal !== undefined && rotina.gasto_da_rotina_kcal !== null

  return (
    <form
      aria-label="Rotina"
      className="flex flex-col gap-4"
      noValidate
      onSubmit={aoEnviar(async (dados) => {
        const gravada = await guardarRotina(dados)
        setRotina(gravada)
        onGuardou(gravada)
      })}
    >
      {temGasto ? <Gasto valor={rotina.gasto_da_rotina_kcal} /> : null}
      {rotina.lifestyle && !temGasto ? (
        <p className="text-sm text-zinc-400">
          O gasto aparece quando o corpo está preenchido.{' '}
          <Link to="/perfil" className="font-medium text-zinc-50">Abrir o corpo</Link>
        </p>
      ) : null}
      <Card>
        <fieldset className="flex flex-col gap-2">
          <legend className="mb-2 text-sm font-medium">Uma rotina</legend>
          <div className="flex flex-col gap-2">
            {opcoes.map(([valor, rotulo, detalhe]) => (
              <Escolha key={valor} valor={valor} rotulo={rotulo} detalhe={detalhe} {...escolha} />
            ))}
          </div>
          {erros.lifestyle?.message ? <p className="text-sm text-red-500" role="alert">{erros.lifestyle.message}</p> : null}
        </fieldset>
      </Card>
      {aviso ? <p className="text-sm text-red-500" role="alert">{aviso}</p> : null}
      <Button type="submit">Guardar</Button>
    </form>
  )
}

export default function Rotina() {
  const [rotina, setRotina] = useState(null)
  const [falha, setFalha] = useState('')

  useEffect(() => {
    let ativa = true

    lerRotina()
      .then((atual) => {
        if (ativa) {
          setRotina(atual)
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
        <h2 className="text-2xl font-semibold tracking-tight">Rotina</h2>
        <p className="text-sm text-zinc-400">O hábito do dia pesa no gasto, fora do treino.</p>
      </div>
      {falha ? <p className="text-sm text-red-500" role="alert">{falha}</p> : null}
      {rotina ? <FormularioRotina inicial={rotina} onGuardou={setRotina} /> : null}
    </div>
  )
}
