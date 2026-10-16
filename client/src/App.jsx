import { useEffect, useState } from 'react'
import { Navigate, Route, Routes } from 'react-router'
import CriarConta from './acesso/CriarConta.jsx'
import Entrar from './acesso/Entrar.jsx'
import Inicio from './acesso/Inicio.jsx'
import Meta from './objetivo/Meta.jsx'
import Corpo from './perfil/Corpo.jsx'
import { sair, sessaoAtual } from './acesso/api.js'
import { mensagem } from './acesso/mensagem.js'
import Cabecalho from './components/Cabecalho.jsx'

export default function App() {
  const [sessao, setSessao] = useState(null)
  const [pronta, setPronta] = useState(false)
  const [erro, setErro] = useState('')

  useEffect(() => {
    let ativa = true

    sessaoAtual()
      .then((atual) => {
        if (ativa) {
          setSessao(atual)
        }
      })
      .catch((falha) => {
        if (ativa) {
          setErro(mensagem(falha))
        }
      })
      .finally(() => {
        if (ativa) {
          setPronta(true)
        }
      })

    return () => {
      ativa = false
    }
  }, [])

  async function aoSair() {
    setErro('')

    try {
      await sair()
      setSessao(null)
    } catch (falha) {
      setErro(mensagem(falha))
    }
  }

  return (
    <div className="min-h-dvh bg-zinc-950 text-zinc-50">
      <div className="mx-auto flex min-h-dvh w-full max-w-md flex-col gap-6 px-4 pt-[max(1.5rem,env(safe-area-inset-top))] pr-[max(1rem,env(safe-area-inset-right))] pb-[max(1.5rem,env(safe-area-inset-bottom))] pl-[max(1rem,env(safe-area-inset-left))]">
        <Cabecalho pronta={pronta} sessao={sessao} onSair={aoSair} />
        {erro ? <p className="text-sm text-red-500" role="alert">{erro}</p> : null}
        {pronta ? (
          <Routes>
            <Route
              path="/entrar"
              element={sessao ? <Navigate to="/" replace /> : <Entrar onEntrou={setSessao} />}
            />
            <Route
              path="/criar-conta"
              element={sessao ? <Navigate to="/" replace /> : <CriarConta onCriou={setSessao} />}
            />
            <Route
              path="/"
              element={
                sessao
                  ? <Inicio sessao={sessao} />
                  : <Navigate to="/entrar" replace />
              }
            />
            <Route
              path="/perfil"
              element={sessao ? <Corpo /> : <Navigate to="/entrar" replace />}
            />
            <Route
              path="/objetivo"
              element={sessao ? <Meta /> : <Navigate to="/entrar" replace />}
            />
            <Route path="*" element={<Navigate to={sessao ? '/' : '/entrar'} replace />} />
          </Routes>
        ) : null}
      </div>
    </div>
  )
}
