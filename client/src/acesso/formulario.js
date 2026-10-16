import { zodResolver } from '@hookform/resolvers/zod'
import { useState } from 'react'
import { useForm } from 'react-hook-form'
import { errosDoCampo, mensagem } from './mensagem.js'

export function useFormularioAcesso(esquema, valores) {
  const [aviso, setAviso] = useState('')
  const { register, handleSubmit, setError, formState: { errors } } = useForm({
    resolver: zodResolver(esquema),
    mode: 'onChange',
    defaultValues: valores,
  })

  function registrar(nome) {
    const campo = register(nome)

    return {
      ...campo,
      onChange: (evento) => {
        setAviso('')
        return campo.onChange(evento)
      },
    }
  }

  function aoEnviar(sucesso) {
    return handleSubmit(async (dados) => {
      setAviso('')

      try {
        await sucesso(dados)
      } catch (falha) {
        const campos = errosDoCampo(falha)

        if (Object.keys(campos).length) {
          for (const [campo, message] of Object.entries(campos)) {
            setError(campo, { message })
          }
          return
        }

        setAviso(mensagem(falha))
      }
    })
  }

  return { registrar, aoEnviar, erros: errors, aviso }
}
