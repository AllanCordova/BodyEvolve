import { fireEvent, render, screen, within } from '@testing-library/react'
import { MemoryRouter } from 'react-router'
import { beforeEach, expect, it, vi } from 'vitest'
import App from '../App.jsx'
import { apiUrl } from '../acesso/api.js'

let sessao = null
let meta = { goal: null }

const alvos = {
  hipertrofia: { kcal: 3131, maintenance_kcal: 2723, protein_g: 160, fat_g: 80, carb_g: 442.75 },
  emagrecimento: { kcal: 2178, maintenance_kcal: 2723, protein_g: 176, fat_g: 64, carb_g: 224.5 },
  forca: {
    kcal: 2859,
    maintenance_kcal: 2723,
    protein_g: 144,
    fat_g: 88,
    carb_g: 320,
  },
  resistencia: {
    kcal: 2723,
    maintenance_kcal: 2723,
    protein_g: 112,
    fat_g: 96,
    carb_g: 352.75,
    carb_warning: 'O carboidrato pedido não coube no saldo. Ficou o que sobrou.',
  },
}

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
  meta = { goal: null }
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

    if (endereco.endsWith('/api/objetivo') && method === 'GET') {
      return json(meta)
    }

    if (endereco.endsWith('/api/objetivo') && method === 'PUT') {
      const pedido = JSON.parse(opcoes.body)
      meta = {
        goal: pedido.goal,
        goal_started_on: '2026-10-16',
        ...alvos[pedido.goal],
      }
      return json(meta)
    }

    return json({ message: 'Not found.' }, 404)
  }))
})

it('envia a meta escolhida e mostra o alvo que a API devolveu', async () => {
  abrir('/entrar')

  const entrar = await screen.findByRole('form', { name: 'Entrar' })

  fireEvent.change(within(entrar).getByLabelText('E-mail'), {
    target: { value: 'ana@example.com' },
  })
  fireEvent.change(within(entrar).getByLabelText('Senha'), {
    target: { value: 'senha-segura' },
  })
  fireEvent.submit(entrar)

  fireEvent.click(await screen.findByRole('link', { name: 'Meta' }))

  const formulario = await screen.findByRole('form', { name: 'Meta' })

  fireEvent.click(within(formulario).getByRole('radio', { name: 'Hipertrofia' }))
  fireEvent.submit(formulario)

  expect(await screen.findByText('3131')).toBeInTheDocument()
  expect(screen.getByText('160')).toBeInTheDocument()
  expect(screen.getByText('80')).toBeInTheDocument()
  expect(screen.getByText('442.75')).toBeInTheDocument()

  fireEvent.click(within(formulario).getByRole('radio', { name: 'Força' }))
  fireEvent.submit(formulario)

  expect((await screen.findAllByText('2859')).length).toBeGreaterThan(0)
  expect(screen.getByText('2723')).toBeInTheDocument()
  expect(screen.getByText(/com 5%/)).toBeInTheDocument()

  fireEvent.click(within(formulario).getByRole('radio', { name: 'Resistência' }))
  fireEvent.submit(formulario)

  expect(await screen.findByText('O carboidrato pedido não coube no saldo. Ficou o que sobrou.')).toBeInTheDocument()

  fireEvent.click(within(formulario).getByRole('radio', { name: 'Emagrecimento' }))
  fireEvent.submit(formulario)

  expect(await screen.findByText('2178')).toBeInTheDocument()
  expect(screen.getByText('176')).toBeInTheDocument()
  expect(screen.queryByText('3131')).toBeNull()

  const gravacoes = vi.mocked(fetch).mock.calls.filter((chamada) => (
    String(chamada[0]).endsWith('/api/objetivo') && chamada[1]?.method === 'PUT'
  ))

  expect(gravacoes).toHaveLength(4)
  expect(gravacoes[0][0]).toBe(`${apiUrl}/api/objetivo`)
  expect(JSON.parse(gravacoes[0][1].body)).toEqual({ goal: 'hipertrofia' })
  expect(JSON.parse(gravacoes[1][1].body)).toEqual({ goal: 'forca' })
  expect(JSON.parse(gravacoes[3][1].body)).toEqual({ goal: 'emagrecimento' })
  expect(gravacoes[0][1].credentials).toBe('include')
  expect(gravacoes[0][1].headers['X-XSRF-TOKEN']).toBe('token-de-teste')
})
