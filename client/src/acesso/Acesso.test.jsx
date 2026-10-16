import { fireEvent, render, screen, waitFor, within } from '@testing-library/react'
import { MemoryRouter } from 'react-router'
import { beforeEach, expect, it, vi } from 'vitest'
import App from '../App.jsx'
import { apiUrl } from './api.js'

let sessao = null

function json(corpo, status = 200) {
  return new Response(JSON.stringify(corpo), {
    status,
    headers: { 'Content-Type': 'application/json' },
  })
}

function abrir(caminho) {
  return render(
    <MemoryRouter initialEntries={[caminho]}>
      <App />
    </MemoryRouter>,
  )
}

beforeEach(() => {
  sessao = null
  document.cookie = 'XSRF-TOKEN=; Max-Age=0'

  vi.stubGlobal('fetch', vi.fn(async (url, opcoes = {}) => {
    const endereco = String(url)
    const method = opcoes.method ?? 'GET'

    if (endereco.endsWith('/sanctum/csrf-cookie')) {
      document.cookie = 'XSRF-TOKEN=token-de-teste'
      return new Response(null, { status: 204 })
    }

    if (endereco.endsWith('/api/acesso/sessao') && method === 'GET') {
      if (!sessao) {
        return json({ message: 'Unauthenticated.' }, 401)
      }

      return json(sessao)
    }

    if (endereco.endsWith('/api/acesso/conta') && method === 'POST') {
      const corpo = JSON.parse(opcoes.body)

      if (!corpo.name) {
        return json({
          message: 'Informe o nome.',
          errors: {
            name: ['Informe o nome.'],
          },
        }, 422)
      }

      sessao = {
        name: corpo.name,
        email: corpo.email,
        icon_url: null,
      }
      return json(sessao, 201)
    }

    if (endereco.endsWith('/api/acesso/sessao') && method === 'POST') {
      const corpo = JSON.parse(opcoes.body)

      if (corpo.email === 'invalido') {
        return json({
          message: 'Informe um e-mail válido.',
          errors: {
            email: ['Informe um e-mail válido.'],
            password: ['Informe a senha.'],
          },
        }, 422)
      }

      sessao = {
        name: 'Ana',
        email: corpo.email,
        icon_url: null,
      }
      return json(sessao)
    }

    if (endereco.endsWith('/api/acesso/sessao') && method === 'DELETE') {
      sessao = null
      return new Response(null, { status: 204 })
    }

    return json({ message: 'Not found.' }, 404)
  }))
})

it('separa criar conta e entrar em rotas diferentes', async () => {
  abrir('/entrar')

  expect(await screen.findByRole('form', { name: 'Entrar' })).toBeInTheDocument()
  expect(screen.queryByRole('form', { name: 'Criar conta' })).toBeNull()

  fireEvent.click(screen.getByRole('link', { name: 'Criar conta' }))

  expect(await screen.findByRole('form', { name: 'Criar conta' })).toBeInTheDocument()
  expect(screen.queryByRole('form', { name: 'Entrar' })).toBeNull()

  fireEvent.click(screen.getByRole('link', { name: 'Entrar' }))

  expect(await screen.findByRole('form', { name: 'Entrar' })).toBeInTheDocument()
  expect(screen.queryByRole('form', { name: 'Criar conta' })).toBeNull()
})

it('entra com o que foi digitado, mostra a conta e esconde o resto sem sessão', async () => {
  abrir('/entrar')

  const entrar = await screen.findByRole('form', { name: 'Entrar' })

  expect(screen.queryByRole('heading', { name: 'Perfil' })).toBeNull()
  expect(screen.queryByRole('heading', { name: 'Treino' })).toBeNull()
  expect(screen.queryByRole('heading', { name: 'Comida' })).toBeNull()
  expect(screen.queryByRole('heading', { name: 'Alertas' })).toBeNull()

  fireEvent.change(within(entrar).getByLabelText('E-mail'), {
    target: { value: 'ana@example.com' },
  })
  fireEvent.change(within(entrar).getByLabelText('Senha'), {
    target: { value: 'senha-segura' },
  })
  fireEvent.submit(entrar)

  expect(await screen.findByText('ana@example.com')).toBeInTheDocument()
  expect(fetch).toHaveBeenCalledWith(
    `${apiUrl}/api/acesso/sessao`,
    expect.objectContaining({
      method: 'POST',
      credentials: 'include',
      body: JSON.stringify({
        email: 'ana@example.com',
        password: 'senha-segura',
      }),
      headers: expect.objectContaining({
        'X-XSRF-TOKEN': 'token-de-teste',
      }),
    }),
  )
  expect(screen.getByRole('heading', { name: 'Perfil' })).toBeInTheDocument()
  expect(screen.getByRole('heading', { name: 'Treino' })).toBeInTheDocument()
  expect(screen.getByRole('heading', { name: 'Comida' })).toBeInTheDocument()
  expect(screen.getByRole('heading', { name: 'Alertas' })).toBeInTheDocument()

  fireEvent.click(screen.getByRole('button', { name: 'Sair' }))

  expect(await screen.findByRole('form', { name: 'Entrar' })).toBeInTheDocument()
  expect(screen.queryByText('ana@example.com')).toBeNull()
  expect(screen.queryByRole('heading', { name: 'Perfil' })).toBeNull()
  expect(screen.queryByRole('heading', { name: 'Treino' })).toBeNull()
  expect(screen.queryByRole('heading', { name: 'Comida' })).toBeNull()
  expect(screen.queryByRole('heading', { name: 'Alertas' })).toBeNull()
})

it('ajusta o erro embaixo do campo enquanto a pessoa escreve', async () => {
  abrir('/entrar')

  const entrar = await screen.findByRole('form', { name: 'Entrar' })
  const email = within(entrar).getByLabelText('E-mail')

  fireEvent.change(email, { target: { value: 'invalido' } })

  const erroEmail = await within(entrar).findByText('Informe um e-mail válido.')

  expect(erroEmail).toHaveAttribute('role', 'alert')
  expect(erroEmail.compareDocumentPosition(email) & Node.DOCUMENT_POSITION_PRECEDING).toBeTruthy()
  expect(within(entrar).queryByText('Informe a senha.')).toBeNull()
  expect(fetch).not.toHaveBeenCalledWith(
    `${apiUrl}/api/acesso/sessao`,
    expect.objectContaining({ method: 'POST' }),
  )

  fireEvent.change(email, { target: { value: 'ana@example.com' } })

  await waitFor(() => {
    expect(within(entrar).queryByText('Informe um e-mail válido.')).toBeNull()
  })

  fireEvent.change(email, { target: { value: 'invalido' } })
  fireEvent.submit(entrar)

  const erroSenha = await within(entrar).findByText('Informe a senha.')

  expect(erroSenha).toHaveAttribute('role', 'alert')
  expect(erroSenha.compareDocumentPosition(within(entrar).getByLabelText('Senha')) & Node.DOCUMENT_POSITION_PRECEDING).toBeTruthy()

  fireEvent.click(screen.getByRole('link', { name: 'Criar conta' }))

  const criar = await screen.findByRole('form', { name: 'Criar conta' })
  const senha = within(criar).getByLabelText('Senha')

  fireEvent.change(senha, { target: { value: 'curta' } })

  expect(await within(criar).findByText('A senha precisa ter pelo menos 8 caracteres.')).toBeInTheDocument()

  fireEvent.change(senha, { target: { value: 'senha-longa' } })

  await waitFor(() => {
    expect(within(criar).queryByText('A senha precisa ter pelo menos 8 caracteres.')).toBeNull()
  })

  fireEvent.submit(criar)

  const erroNome = await within(criar).findByText('Informe o nome.')

  expect(erroNome).toHaveAttribute('role', 'alert')
  expect(erroNome.compareDocumentPosition(within(criar).getByLabelText('Nome')) & Node.DOCUMENT_POSITION_PRECEDING).toBeTruthy()
})

it('cria a conta com nome, e-mail e senha', async () => {
  abrir('/criar-conta')

  const criar = await screen.findByRole('form', { name: 'Criar conta' })

  expect(within(criar).queryByLabelText('Ícone')).toBeNull()

  fireEvent.change(within(criar).getByLabelText('Nome'), {
    target: { value: 'Bia' },
  })
  fireEvent.change(within(criar).getByLabelText('E-mail'), {
    target: { value: 'bia@example.com' },
  })
  fireEvent.change(within(criar).getByLabelText('Senha'), {
    target: { value: 'outra-senha' },
  })
  fireEvent.submit(criar)

  expect(await screen.findByText('bia@example.com')).toBeInTheDocument()
  expect(fetch).toHaveBeenCalledWith(
    `${apiUrl}/api/acesso/conta`,
    expect.objectContaining({
      method: 'POST',
      credentials: 'include',
      body: JSON.stringify({
        name: 'Bia',
        email: 'bia@example.com',
        password: 'outra-senha',
      }),
    }),
  )
})
