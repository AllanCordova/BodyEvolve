import { Link } from 'react-router'
import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import Campo from './Campo.jsx'
import { entrar } from './api.js'
import { esquemaEntrar } from './esquema.js'
import { useFormularioAcesso } from './formulario.js'

export default function Entrar({ onEntrou }) {
  const { registrar, aoEnviar, erros, aviso } = useFormularioAcesso(esquemaEntrar, {
    email: '',
    password: '',
  })

  return (
    <Card>
      <form
        aria-label="Entrar"
        className="flex flex-col gap-4"
        noValidate
        onSubmit={aoEnviar(async (dados) => {
          onEntrou(await entrar(dados))
        })}
      >
        <h2 className="text-lg font-medium">Entrar</h2>
        <Campo rotulo="E-mail" type="email" autoComplete="email" erro={erros.email?.message} {...registrar('email')} />
        <Campo rotulo="Senha" type="password" autoComplete="current-password" erro={erros.password?.message} {...registrar('password')} />
        {aviso ? <p className="text-sm text-red-500" role="alert">{aviso}</p> : null}
        <Button type="submit">Entrar</Button>
        <Button variant="outline" asChild>
          <Link to="/criar-conta">Criar conta</Link>
        </Button>
      </form>
    </Card>
  )
}
