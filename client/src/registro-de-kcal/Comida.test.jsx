import { fireEvent, render, screen, within } from '@testing-library/react'
import { MemoryRouter } from 'react-router'
import { beforeEach, expect, it, vi } from 'vitest'
import App from '../App.jsx'
import { apiUrl } from '../acesso/api.js'

let sessao = null
let comida = {
  intake_date: '2026-10-19',
  kcal: null,
  protein_g: null,
  fat_g: null,
  carb_g: null,
  fiber_g: null,
}

function json(corpoResposta, status = 200) {
  return new Response(JSON.stringify(corpoResposta), {
    status,
    headers: { 'Content-Type': 'application/json' },
  })
}

beforeEach(() => {
  sessao = null
  comida = {
    intake_date: '2026-10-19',
    kcal: null,
    protein_g: null,
    fat_g: null,
    carb_g: null,
    fiber_g: null,
  }
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

    if (endereco.endsWith('/api/comida') && method === 'GET') {
      return json(comida)
    }

    if (endereco.endsWith('/api/comida') && method === 'PUT') {
      comida = { intake_date: '2026-10-19', ...JSON.parse(opcoes.body) }
      return json(comida)
    }

    return json({ message: 'Not found.' }, 404)
  }))
})

it('envia a comida do dia e mostra os números que a API devolveu', async () => {
  render(
    <MemoryRouter initialEntries={['/entrar']}>
      <App />
    </MemoryRouter>,
  )

  const entrar = await screen.findByRole('form', { name: 'Entrar' })
  fireEvent.change(within(entrar).getByLabelText('E-mail'), { target: { value: 'ana@example.com' } })
  fireEvent.change(within(entrar).getByLabelText('Senha'), { target: { value: 'senha-segura' } })
  fireEvent.submit(entrar)

  fireEvent.click(await screen.findByRole('link', { name: 'Comida' }))

  const formulario = await screen.findByRole('form', { name: 'Comida' })
  fireEvent.change(within(formulario).getByLabelText('Calorias'), { target: { value: '2200' } })
  fireEvent.change(within(formulario).getByLabelText('Proteína'), { target: { value: '150' } })
  fireEvent.change(within(formulario).getByLabelText('Gordura'), { target: { value: '70' } })
  fireEvent.change(within(formulario).getByLabelText('Carboidrato'), { target: { value: '200' } })
  fireEvent.change(within(formulario).getByLabelText('Fibra'), { target: { value: '28' } })
  fireEvent.submit(formulario)

  expect(await screen.findByText('2200')).toBeInTheDocument()
  expect(screen.getByText('28')).toBeInTheDocument()

  const gravacao = vi.mocked(fetch).mock.calls.find((chamada) => (
    String(chamada[0]).endsWith('/api/comida') && chamada[1]?.method === 'PUT'
  ))

  expect(gravacao[0]).toBe(`${apiUrl}/api/comida`)
  expect(JSON.parse(gravacao[1].body)).toEqual({
    kcal: 2200,
    protein_g: 150,
    fat_g: 70,
    carb_g: 200,
    fiber_g: 28,
  })
  expect(gravacao[1].headers['X-XSRF-TOKEN']).toBe('token-de-teste')
})
