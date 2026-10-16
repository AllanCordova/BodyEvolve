import { LogOut, User } from 'lucide-react'
import { Link } from 'react-router'
import Button from './ui/button.jsx'

const acao = 'size-11 w-11 shrink-0 p-0'

export default function Cabecalho({ pronta, sessao, onSair }) {
  return (
    <header className="flex items-center justify-between gap-3">
      <div className="flex min-w-0 items-center gap-3">
        <img
          src="/screen.png"
          alt=""
          width="44"
          height="44"
          className="size-11 rounded-xl"
        />
        <h1 className="truncate text-xl font-semibold tracking-tight">BodyEvolve</h1>
      </div>
      {pronta ? (
        sessao ? (
          <Button
            variant="outline"
            type="button"
            aria-label="Sair"
            className={acao}
            onClick={onSair}
          >
            <LogOut />
          </Button>
        ) : (
          <Button variant="outline" asChild className={acao}>
            <Link to="/entrar" aria-label="Acessar">
              <User />
            </Link>
          </Button>
        )
      ) : (
        <span className="size-11 shrink-0" />
      )}
    </header>
  )
}
