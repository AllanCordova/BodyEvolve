import { useState } from 'react'
import { Link } from 'react-router'
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
    <form aria-label="Entrar" onSubmit={aoEnviar}>
      <h2>Entrar</h2>
      <label>
        E-mail
        <input name="email" type="email" autoComplete="email" />
      </label>
      <label>
        Senha
        <input name="password" type="password" autoComplete="current-password" />
      </label>
      {erro ? <p role="alert">{erro}</p> : null}
      <button type="submit">Entrar</button>
      <p>
        <Link to="/criar-conta">Criar conta</Link>
      </p>
    </form>
  )
}
