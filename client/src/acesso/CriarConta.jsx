import { useState } from 'react'
import { Link } from 'react-router'
import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import Input from '../components/ui/input.jsx'
import Label from '../components/ui/label.jsx'
import { criarConta } from './api.js'
import { mensagem } from './mensagem.js'

export default function CriarConta({ onCriou }) {
  const [erro, setErro] = useState('')

  async function aoEnviar(evento) {
    evento.preventDefault()
    setErro('')
    const dados = new FormData(evento.currentTarget)

    try {
      onCriou(await criarConta({
        name: String(dados.get('name') ?? ''),
        email: String(dados.get('email') ?? ''),
        password: String(dados.get('password') ?? ''),
      }))
    } catch (falha) {
      setErro(mensagem(falha))
    }
  }

  return (
    <Card>
      <form aria-label="Criar conta" className="flex flex-col gap-4" onSubmit={aoEnviar}>
        <h2 className="text-lg font-medium">Criar conta</h2>
        <Label className="flex flex-col gap-2">
          Nome
          <Input name="name" type="text" autoComplete="name" />
        </Label>
        <Label className="flex flex-col gap-2">
          E-mail
          <Input name="email" type="email" autoComplete="email" />
        </Label>
        <Label className="flex flex-col gap-2">
          Senha
          <Input name="password" type="password" autoComplete="new-password" />
        </Label>
        {erro ? <p role="alert">{erro}</p> : null}
        <Button type="submit">Criar conta</Button>
        <Button variant="outline" asChild>
          <Link to="/entrar">Entrar</Link>
        </Button>
      </form>
    </Card>
  )
}
