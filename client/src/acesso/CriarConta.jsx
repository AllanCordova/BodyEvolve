import { Link } from 'react-router'
import Button from '../components/ui/button.jsx'
import Card from '../components/ui/card.jsx'
import Campo from './Campo.jsx'
import { criarConta } from './api.js'
import { esquemaConta } from './esquema.js'
import { useFormularioAcesso } from './formulario.js'

export default function CriarConta({ onCriou }) {
  const { registrar, aoEnviar, erros, aviso } = useFormularioAcesso(esquemaConta, {
    name: '',
    email: '',
    password: '',
  })

  return (
    <Card>
      <form
        aria-label="Criar conta"
        className="flex flex-col gap-4"
        noValidate
        onSubmit={aoEnviar(async (dados) => {
          onCriou(await criarConta(dados))
        })}
      >
        <h2 className="text-lg font-medium">Criar conta</h2>
        <Campo rotulo="Nome" type="text" autoComplete="name" erro={erros.name?.message} {...registrar('name')} />
        <Campo rotulo="E-mail" type="email" autoComplete="email" erro={erros.email?.message} {...registrar('email')} />
        <Campo rotulo="Senha" type="password" autoComplete="new-password" erro={erros.password?.message} {...registrar('password')} />
        {aviso ? <p className="text-sm text-red-500" role="alert">{aviso}</p> : null}
        <Button type="submit">Criar conta</Button>
        <Button variant="outline" asChild>
          <Link to="/entrar">Entrar</Link>
        </Button>
      </form>
    </Card>
  )
}
