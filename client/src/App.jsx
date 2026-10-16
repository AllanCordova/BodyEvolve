import { useEffect, useState } from 'react'
import { Navigate, Route, Routes } from 'react-router'
import CriarConta from './acesso/CriarConta.jsx'
import Entrar from './acesso/Entrar.jsx'
import Inicio from './acesso/Inicio.jsx'
import { sessaoAtual } from './acesso/api.js'
import { mensagem } from './acesso/mensagem.js'

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

  return (
    <>
      <h1>BodyEvolve</h1>
      {erro ? <p role="alert">{erro}</p> : null}
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
                ? <Inicio sessao={sessao} onSaiu={() => setSessao(null)} />
                : <Navigate to="/entrar" replace />
            }
          />
          <Route path="*" element={<Navigate to={sessao ? '/' : '/entrar'} replace />} />
        </Routes>
      ) : null}
    </>
  )
}
