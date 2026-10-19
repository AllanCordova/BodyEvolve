import { useEffect, useState } from 'react'
import { mensagem } from '../acesso/mensagem.js'
import { useFormularioAcesso } from '../acesso/formulario.js'
import Voltar from '../components/Voltar.jsx'
import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import Input from '../components/ui/input.jsx'
import Label from '../components/ui/label.jsx'
import { guardarComida, lerComida } from './api.js'
import { esquemaComida } from './esquema.js'

const campos = [
  ['kcal', 'Calorias', 'kcal'],
  ['protein_g', 'Proteína', 'g'],
  ['fat_g', 'Gordura', 'g'],
  ['carb_g', 'Carboidrato', 'g'],
  ['fiber_g', 'Fibra', 'g'],
]

function Resumo({ comida }) {
  if (comida.kcal === null || comida.kcal === undefined) {
    return null
  }

  return (
    <Card className="grid grid-cols-2 gap-3">
      {campos.map(([chave, nome, unidade]) => (
        <p key={chave} className="flex flex-col">
          <span className="text-xs text-zinc-400">{nome}</span>
          <span className="text-lg font-medium tabular-nums">
            {comida[chave]}
            <span className="text-xs font-normal text-zinc-400"> {unidade}</span>
          </span>
        </p>
      ))}
    </Card>
  )
}

function Formulario({ onGuardou }) {
  const { registrar, aoEnviar, erros, aviso } = useFormularioAcesso(esquemaComida, {
    kcal: '',
    protein_g: '',
    fat_g: '',
    carb_g: '',
    fiber_g: '',
  })

  return (
    <form
      aria-label="Comida"
      className="flex flex-col gap-4"
      noValidate
      onSubmit={aoEnviar(async (dados) => {
        onGuardou(await guardarComida({
          kcal: Number(dados.kcal),
          protein_g: Number(dados.protein_g),
          fat_g: Number(dados.fat_g),
          carb_g: Number(dados.carb_g),
          fiber_g: Number(dados.fiber_g),
        }))
      })}
    >
      <Card className="flex flex-col gap-3">
        {campos.map(([chave, nome]) => {
          const campo = registrar(chave)

          return (
            <div key={chave} className="flex flex-col gap-2">
              <Label htmlFor={chave}>{nome}</Label>
              <Input id={chave} inputMode="decimal" {...campo} />
              {erros[chave]?.message ? <p className="text-sm text-red-500" role="alert">{erros[chave].message}</p> : null}
            </div>
          )
        })}
      </Card>
      {aviso ? <p className="text-sm text-red-500" role="alert">{aviso}</p> : null}
      <Button type="submit">Registrar o dia</Button>
    </form>
  )
}

export default function Comida() {
  const [comida, setComida] = useState(null)
  const [falha, setFalha] = useState('')

  useEffect(() => {
    let ativa = true

    lerComida()
      .then((atual) => {
        if (ativa) {
          setComida(atual)
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
        <h2 className="text-2xl font-semibold tracking-tight">Comida</h2>
        <p className="text-sm text-zinc-400">Um registro do dia, para comparar com o alvo.</p>
      </div>
      {falha ? <p className="text-sm text-red-500" role="alert">{falha}</p> : null}
      {comida ? <Resumo comida={comida} /> : null}
      <Formulario onGuardou={setComida} />
    </div>
  )
}
