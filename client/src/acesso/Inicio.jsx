import { sair } from './api.js'

export default function Inicio({ sessao, onSaiu }) {
  async function aoSair() {
    await sair()
    onSaiu()
  }

  return (
    <>
      <p>
        Conta: <span>{sessao.name}</span> <span>{sessao.email}</span>
      </p>
      {sessao.icon_url ? <img src={sessao.icon_url} alt="" width="64" height="64" /> : null}
      <button type="button" onClick={aoSair}>
        Sair
      </button>
      <section>
        <h2>Perfil</h2>
      </section>
      <section>
        <h2>Treino</h2>
      </section>
      <section>
        <h2>Comida</h2>
      </section>
      <section>
        <h2>Alertas</h2>
      </section>
    </>
  )
}
