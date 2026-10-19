import { fireEvent, render, screen, within } from '@testing-library/react'
import { MemoryRouter } from 'react-router'
import { beforeEach, expect, it, vi } from 'vitest'
import App from '../App.jsx'
import { apiUrl } from '../acesso/api.js'

let sessao = null
let manutencao = { basal_kcal: null, lifestyle: null }

function json(corpoResposta, status = 200) {
  return new Response(JSON.stringify(corpoResposta), {
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
  manutencao = { basal_kcal: null, lifestyle: null }
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

    if (endereco.endsWith('/api/acesso/sessao') && method === 'POST') {
      sessao = {
        name: 'Ana',
        email: 'ana@example.com',
        icon_url: null,
      }
      return json(sessao)
    }

    if (endereco.endsWith('/api/manutencao') && method === 'GET') {
      return json(manutencao)
    }

    return json({ message: 'Not found.' }, 404)
  }))
})

it('mostra a manutenção que a API devolveu', async () => {
  manutencao = {
    basal_kcal: 1780,
    lifestyle: 'sedentaria',
    maintenance_kcal: 2723,
  }

  abrir('/entrar')

  const entrar = await screen.findByRole('form', { name: 'Entrar' })

  fireEvent.change(within(entrar).getByLabelText('E-mail'), {
    target: { value: 'ana@example.com' },
  })
  fireEvent.change(within(entrar).getByLabelText('Senha'), {
    target: { value: 'senha-segura' },
  })
  fireEvent.submit(entrar)

  fireEvent.click(await screen.findByRole('link', { name: 'Manutenção' }))

  expect(await screen.findByText('2723')).toBeInTheDocument()
  expect(screen.getByText('1780')).toBeInTheDocument()

  const leitura = vi.mocked(fetch).mock.calls.find((chamada) => (
    String(chamada[0]).endsWith('/api/manutencao')
  ))

  expect(leitura[0]).toBe(`${apiUrl}/api/manutencao`)
  expect(leitura[1]?.credentials ?? 'include').toBe('include')
})

it('pede corpo e rotina quando a API ainda não tem manutenção', async () => {
  abrir('/entrar')

  const entrar = await screen.findByRole('form', { name: 'Entrar' })
  fireEvent.change(within(entrar).getByLabelText('E-mail'), {
    target: { value: 'ana@example.com' },
  })
  fireEvent.change(within(entrar).getByLabelText('Senha'), {
    target: { value: 'senha-segura' },
  })
  fireEvent.submit(entrar)

  fireEvent.click(await screen.findByRole('link', { name: 'Manutenção' }))

  expect(await screen.findByText(/quando o corpo e a rotina estão preenchidos/)).toBeInTheDocument()
  expect(screen.queryByText('2723')).toBeNull()
})
