import { useEffect, useState } from 'react'
import { Link } from 'react-router'
import { mensagem } from '../acesso/mensagem.js'
import { useFormularioAcesso } from '../acesso/formulario.js'
import Campo from '../acesso/Campo.jsx'
import Voltar from '../components/Voltar.jsx'
import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import Escolha from '../components/ui/escolha.jsx'
import { guardarCorpo, lerCorpo } from './api.js'
import { esquemaCorpo } from './esquema.js'

function texto(valor) {
  return valor === null || valor === undefined ? '' : String(valor)
}

function Gasto({ valor }) {
  return (
    <Card className="flex flex-col gap-2">
      <p className="text-sm text-zinc-400">Gasto em repouso</p>
      <p className="flex items-baseline gap-2">
        <span className="text-4xl font-semibold tracking-tight text-emerald-500 tabular-nums">{valor}</span>
        <span className="text-sm text-zinc-400">kcal</span>
      </p>
      <Link to="/objetivo" className="inline-flex min-h-11 items-center text-sm font-medium text-zinc-50">
        Ver o alvo
      </Link>
    </Card>
  )
}

function FormularioCorpo({ inicial, onGuardou }) {
  const [gasto, setGasto] = useState(inicial.gasto_repouso_kcal ?? null)
  const { registrar, aoEnviar, erros, aviso } = useFormularioAcesso(esquemaCorpo, {
    age_years: texto(inicial.age_years),
    sex: inicial.sex ?? '',
    weight_kg: texto(inicial.weight_kg),
    height_cm: texto(inicial.height_cm),
  })
  const sexo = registrar('sex')

  return (
    <form
      aria-label="Corpo"
      className="flex flex-col gap-4"
      noValidate
      onSubmit={aoEnviar(async (dados) => {
        const gravado = await guardarCorpo(dados)
        setGasto(gravado.gasto_repouso_kcal ?? null)
        onGuardou(gravado)
      })}
    >
      {gasto === null ? null : <Gasto valor={gasto} />}
      <Card className="flex flex-col gap-4">
        <Campo rotulo="Idade" type="number" inputMode="numeric" min="1" step="1" erro={erros.age_years?.message} {...registrar('age_years')} />
        <fieldset className="flex flex-col gap-2">
          <legend className="text-sm font-medium">Sexo biológico</legend>
          <div className="grid grid-cols-2 gap-2">
            <Escolha valor="masculino" rotulo="Masculino" {...sexo} />
            <Escolha valor="feminino" rotulo="Feminino" {...sexo} />
          </div>
          {erros.sex?.message ? <p className="text-sm text-red-500" role="alert">{erros.sex.message}</p> : null}
        </fieldset>
        <Campo rotulo="Peso" unidade="kg" type="number" inputMode="decimal" min="0" step="0.1" erro={erros.weight_kg?.message} {...registrar('weight_kg')} />
        <Campo rotulo="Altura" unidade="cm" type="number" inputMode="numeric" min="1" step="1" erro={erros.height_cm?.message} {...registrar('height_cm')} />
      </Card>
      {aviso ? <p className="text-sm text-red-500" role="alert">{aviso}</p> : null}
      <Button type="submit">Guardar</Button>
    </form>
  )
}

export default function Corpo() {
  const [corpo, setCorpo] = useState(null)
  const [falha, setFalha] = useState('')

  useEffect(() => {
    let ativa = true

    lerCorpo()
      .then((atual) => {
        if (ativa) {
          setCorpo(atual)
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
        <h2 className="text-2xl font-semibold tracking-tight">Corpo</h2>
        <p className="text-sm text-zinc-400">Idade, sexo, peso e altura entram no gasto em repouso.</p>
      </div>
      {falha ? <p className="text-sm text-red-500" role="alert">{falha}</p> : null}
      {corpo ? <FormularioCorpo inicial={corpo} onGuardou={setCorpo} /> : null}
    </div>
  )
}
