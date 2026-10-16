import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import { sair } from './api.js'

export default function Inicio({ sessao, onSaiu }) {
  async function aoSair() {
    await sair()
    onSaiu()
  }

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
        <h2 className="text-lg font-medium">Perfil</h2>
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
      <Button variant="outline" type="button" onClick={aoSair}>
        Sair
      </Button>
    </div>
  )
}
