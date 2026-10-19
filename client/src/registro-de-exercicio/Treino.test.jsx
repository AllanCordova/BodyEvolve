import { fireEvent, render, screen, within } from '@testing-library/react'
import { MemoryRouter } from 'react-router'
import { beforeEach, expect, it, vi } from 'vitest'
import App from '../App.jsx'
import { apiUrl } from '../acesso/api.js'

let sessao = null

const modalidades = [
  {
    code: 'caminhada-rapida',
    name: 'Caminhada rápida',
    catalog: 'cardio',
    intensities: [
      { code: 'padrao', name: 'Padrão', factor: 1 },
      { code: 'muito-rapida', name: 'Muito rápida', factor: 1.15 },
    ],
  },
]

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

    if (endereco.endsWith('/api/treino/modalidades')) {
      return json(modalidades)
    }

    if (endereco.endsWith('/api/treino/exercicios')) {
      return json([])
    }

    if (endereco.endsWith('/api/treino/progressao')) {
      return json([{
        exercise_code: 'agachamento',
        exercise_name: 'Agachamento',
        state: 'progrediu',
        state_label: 'Progrediu',
        message: null,
        suggested_load_kg: null,
      }])
    }

    if (endereco.endsWith('/api/treino/sessoes') && method === 'GET') {
      return json([])
    }

    if (endereco.endsWith('/api/treino/sessoes') && method === 'POST') {
      const pedido = JSON.parse(opcoes.body)
      return json({
        id: 'sessao-1',
        modality_name: 'Caminhada rápida',
        net_kcal: 262.2,
        ...pedido,
      }, 201)
    }

    return json({ message: 'Not found.' }, 404)
  }))
})

it('envia a sessão e mostra o gasto líquido que a API devolveu', async () => {
  render(
    <MemoryRouter initialEntries={['/entrar']}>
      <App />
    </MemoryRouter>,
  )

  const entrar = await screen.findByRole('form', { name: 'Entrar' })
  fireEvent.change(within(entrar).getByLabelText('E-mail'), { target: { value: 'ana@example.com' } })
  fireEvent.change(within(entrar).getByLabelText('Senha'), { target: { value: 'senha-segura' } })
  fireEvent.submit(entrar)

  fireEvent.click(await screen.findByRole('link', { name: 'Treino' }))

  expect(await screen.findByText('Progrediu')).toBeInTheDocument()
  expect(screen.getByText('Agachamento')).toBeInTheDocument()

  const formulario = await screen.findByRole('form', { name: 'Treino' })
  fireEvent.change(within(formulario).getByLabelText('Intensidade'), { target: { value: 'muito-rapida' } })
  fireEvent.change(within(formulario).getByLabelText('Minutos'), { target: { value: '45' } })
  fireEvent.click(within(formulario).getByLabelText('Essa hora já faz parte da rotina'))
  fireEvent.submit(formulario)

  expect(await screen.findByText('262.2')).toBeInTheDocument()

  const gravacao = vi.mocked(fetch).mock.calls.find((chamada) => (
    String(chamada[0]).endsWith('/api/treino/sessoes') && chamada[1]?.method === 'POST'
  ))

  expect(gravacao[0]).toBe(`${apiUrl}/api/treino/sessoes`)
  expect(JSON.parse(gravacao[1].body)).toEqual({
    modality_code: 'caminhada-rapida',
    duration_min: 45,
    intensity_code: 'muito-rapida',
    already_in_lifestyle: true,
  })
  expect(gravacao[1].credentials).toBe('include')
  expect(gravacao[1].headers['X-XSRF-TOKEN']).toBe('token-de-teste')
})
