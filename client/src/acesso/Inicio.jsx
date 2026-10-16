import { Link } from 'react-router'
import Card from '../components/ui/card.jsx'

export default function Inicio({ sessao }) {
  return (
    <div className="flex flex-col gap-4">
      <Card>
        <p>
          Conta: <span>{sessao.name}</span> <span>{sessao.email}</span>
        </p>
        {sessao.icon_url ? (
          <img
            src={sessao.icon_url}
            alt=""
            width="64"
            height="64"
            className="mt-3 size-16 rounded-md object-cover"
          />
        ) : null}
      </Card>
      <Card>
        <h2 className="text-lg font-medium">
          <Link to="/perfil" className="flex min-h-11 items-center">Perfil</Link>
        </h2>
      </Card>
      <Card>
        <h2 className="text-lg font-medium">Treino</h2>
      </Card>
      <Card>
        <h2 className="text-lg font-medium">Comida</h2>
      </Card>
      <Card>
        <h2 className="text-lg font-medium">Alertas</h2>
      </Card>
    </div>
  )
}
