import { useState } from 'react'
import { Link } from 'react-router'
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
    <form aria-label="Criar conta" onSubmit={aoEnviar}>
      <h2>Criar conta</h2>
      <label>
        Nome
        <input name="name" type="text" autoComplete="name" />
      </label>
      <label>
        E-mail
        <input name="email" type="email" autoComplete="email" />
      </label>
      <label>
        Senha
        <input name="password" type="password" autoComplete="new-password" />
      </label>
      {erro ? <p role="alert">{erro}</p> : null}
      <button type="submit">Criar conta</button>
      <p>
        <Link to="/entrar">Entrar</Link>
      </p>
    </form>
  )
}
