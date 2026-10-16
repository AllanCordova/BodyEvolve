import { fireEvent, render, screen, within } from '@testing-library/react'
import { MemoryRouter } from 'react-router'
import { beforeEach, expect, it, vi } from 'vitest'
import App from '../App.jsx'
import { apiUrl } from '../acesso/api.js'

let sessao = null
let rotina = { lifestyle: null }

const gastos = {
  sedentaria: 2723,
  moderada: 3133,
  vigorosa: 4005,
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
  rotina = { lifestyle: null }
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

    if (endereco.endsWith('/api/perfil/rotina') && method === 'GET') {
      return json(rotina)
    }

    if (endereco.endsWith('/api/perfil/rotina') && method === 'PUT') {
      const pedido = JSON.parse(opcoes.body)
      rotina = {
        lifestyle: pedido.lifestyle,
        gasto_da_rotina_kcal: gastos[pedido.lifestyle],
      }
      return json(rotina)
    }

    return json({ message: 'Not found.' }, 404)
  }))
})

it('envia a rotina escolhida e mostra o gasto que a API devolveu', async () => {
  abrir('/entrar')

  const entrar = await screen.findByRole('form', { name: 'Entrar' })

  fireEvent.change(within(entrar).getByLabelText('E-mail'), {
    target: { value: 'ana@example.com' },
  })
  fireEvent.change(within(entrar).getByLabelText('Senha'), {
    target: { value: 'senha-segura' },
  })
  fireEvent.submit(entrar)

  fireEvent.click(await screen.findByRole('link', { name: 'Rotina' }))

  const formulario = await screen.findByRole('form', { name: 'Rotina' })

  fireEvent.click(within(formulario).getByRole('radio', { name: 'Sedentária' }))
  fireEvent.submit(formulario)

  expect(await screen.findByText('2723')).toBeInTheDocument()

  fireEvent.click(within(formulario).getByRole('radio', { name: 'Moderada' }))
  fireEvent.submit(formulario)

  expect(await screen.findByText('3133')).toBeInTheDocument()
  expect(screen.queryByText('2723')).toBeNull()

  fireEvent.click(within(formulario).getByRole('radio', { name: 'Vigorosa' }))
  fireEvent.submit(formulario)

  expect(await screen.findByText('4005')).toBeInTheDocument()
  expect(screen.queryByText('3133')).toBeNull()

  const gravacoes = vi.mocked(fetch).mock.calls.filter((chamada) => (
    String(chamada[0]).endsWith('/api/perfil/rotina') && chamada[1]?.method === 'PUT'
  ))

  expect(gravacoes).toHaveLength(3)
  expect(gravacoes[0][0]).toBe(`${apiUrl}/api/perfil/rotina`)
  expect(JSON.parse(gravacoes[0][1].body)).toEqual({ lifestyle: 'sedentaria' })
  expect(JSON.parse(gravacoes[1][1].body)).toEqual({ lifestyle: 'moderada' })
  expect(JSON.parse(gravacoes[2][1].body)).toEqual({ lifestyle: 'vigorosa' })
  expect(gravacoes[0][1].credentials).toBe('include')
  expect(gravacoes[0][1].headers['X-XSRF-TOKEN']).toBe('token-de-teste')
})
