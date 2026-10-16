import { useState } from 'react'
import { Link } from 'react-router'
import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import Input from '../components/ui/input.jsx'
import Label from '../components/ui/label.jsx'
import { entrar } from './api.js'
import { mensagem } from './mensagem.js'

export default function Entrar({ onEntrou }) {
  const [erro, setErro] = useState('')

  async function aoEnviar(evento) {
    evento.preventDefault()
    setErro('')
    const dados = new FormData(evento.currentTarget)

    try {
      onEntrou(await entrar({
        email: String(dados.get('email') ?? ''),
        password: String(dados.get('password') ?? ''),
      }))
    } catch (falha) {
      setErro(mensagem(falha))
    }
  }

  return (
    <Card>
      <form aria-label="Entrar" className="flex flex-col gap-4" onSubmit={aoEnviar}>
        <h2 className="text-lg font-medium">Entrar</h2>
        <Label className="flex flex-col gap-2">
          E-mail
          <Input name="email" type="email" autoComplete="email" />
        </Label>
        <Label className="flex flex-col gap-2">
          Senha
          <Input name="password" type="password" autoComplete="current-password" />
        </Label>
        {erro ? <p role="alert">{erro}</p> : null}
        <Button type="submit">Entrar</Button>
        <Button variant="outline" asChild>
          <Link to="/criar-conta">Criar conta</Link>
        </Button>
      </form>
    </Card>
  )
}
