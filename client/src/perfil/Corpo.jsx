import { useEffect, useState } from 'react'
import { Link } from 'react-router'
import { mensagem } from '../acesso/mensagem.js'
import { useFormularioAcesso } from '../acesso/formulario.js'
import Campo from '../acesso/Campo.jsx'
import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import { guardarCorpo, lerCorpo } from './api.js'
import { esquemaCorpo } from './esquema.js'

function texto(valor) {
  return valor === null || valor === undefined ? '' : String(valor)
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
    <Card>
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
        <h2 className="text-lg font-medium">Corpo</h2>
        <Campo rotulo="Idade" type="number" inputMode="numeric" min="1" step="1" erro={erros.age_years?.message} {...registrar('age_years')} />
        <fieldset className="flex flex-col gap-2">
          <legend className="text-sm font-medium">Sexo biológico</legend>
          <div className="flex flex-col gap-2">
            <label className="flex min-h-11 items-center gap-3 rounded-md border border-zinc-800 px-3">
              <input type="radio" value="masculino" className="size-4 accent-emerald-500" {...sexo} />
              Masculino
            </label>
            <label className="flex min-h-11 items-center gap-3 rounded-md border border-zinc-800 px-3">
              <input type="radio" value="feminino" className="size-4 accent-emerald-500" {...sexo} />
              Feminino
            </label>
          </div>
          {erros.sex?.message ? <p className="text-sm text-red-500" role="alert">{erros.sex.message}</p> : null}
        </fieldset>
        <Campo rotulo="Peso" unidade="kg" type="number" inputMode="decimal" min="0" step="0.1" erro={erros.weight_kg?.message} {...registrar('weight_kg')} />
        <Campo rotulo="Altura" unidade="cm" type="number" inputMode="numeric" min="1" step="1" erro={erros.height_cm?.message} {...registrar('height_cm')} />
        {gasto === null ? null : (
          <p>
            Gasto em repouso <span>{gasto}</span> <span className="text-zinc-400">kcal</span>
          </p>
        )}
        {aviso ? <p className="text-sm text-red-500" role="alert">{aviso}</p> : null}
        <Button type="submit">Guardar</Button>
        <Button variant="outline" asChild>
          <Link to="/">Início</Link>
        </Button>
      </form>
    </Card>
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

  if (falha) {
    return <p className="text-sm text-red-500" role="alert">{falha}</p>
  }

  if (!corpo) {
    return null
  }

  return <FormularioCorpo inicial={corpo} onGuardou={setCorpo} />
}
