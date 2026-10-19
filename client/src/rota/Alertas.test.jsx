import { fireEvent, render, screen, within } from '@testing-library/react'
import { MemoryRouter } from 'react-router'
import { beforeEach, expect, it, vi } from 'vitest'
import App from '../App.jsx'

let sessao = null

function json(corpoResposta, status = 200) {
  return new Response(JSON.stringify(corpoResposta), {
    status,
    headers: { 'Content-Type': 'application/json' },
  })
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
      return sessao ? json(sessao) : json({ message: 'Unauthenticated.' }, 401)
    }

    if (endereco.endsWith('/api/acesso/sessao') && method === 'POST') {
      sessao = { name: 'Ana', email: 'ana@example.com', icon_url: null }
      return json(sessao)
    }

    if (endereco.endsWith('/api/alertas')) {
      return json([{
        kind: 'deficit_na_hipertrofia',
        message: 'Suba a comida. Essa meta pede 15% acima da manutenção.',
        fails_goal: true,
      }])
    }

    return json({ message: 'Not found.' }, 404)
  }))
})

it('mostra o aviso que a API devolveu', async () => {
  render(
    <MemoryRouter initialEntries={['/entrar']}>
      <App />
    </MemoryRouter>,
  )

  const entrar = await screen.findByRole('form', { name: 'Entrar' })
  fireEvent.change(within(entrar).getByLabelText('E-mail'), { target: { value: 'ana@example.com' } })
  fireEvent.change(within(entrar).getByLabelText('Senha'), { target: { value: 'senha-segura' } })
  fireEvent.submit(entrar)

  fireEvent.click(await screen.findByRole('link', { name: 'Alertas' }))

  expect(await screen.findByText('Suba a comida. Essa meta pede 15% acima da manutenção.')).toBeInTheDocument()
})
