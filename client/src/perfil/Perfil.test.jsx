import { fireEvent, render, screen, within } from '@testing-library/react'
import { MemoryRouter } from 'react-router'
import { beforeEach, expect, it, vi } from 'vitest'
import App from '../App.jsx'
import { apiUrl } from '../acesso/api.js'

let sessao = null
let corpo = null

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
  corpo = {
    age_years: null,
    sex: null,
    height_cm: null,
    weight_kg: null,
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

    if (endereco.endsWith('/api/perfil') && method === 'GET') {
      return json(corpo)
    }

    if (endereco.endsWith('/api/perfil') && method === 'PUT') {
      corpo = {
        ...JSON.parse(opcoes.body),
        gasto_repouso_kcal: 1780,
      }
      return json(corpo)
    }

    return json({ message: 'Not found.' }, 404)
  }))
})

it('envia o corpo digitado e mostra o gasto que a API devolveu', async () => {
  abrir('/entrar')

  const entrar = await screen.findByRole('form', { name: 'Entrar' })

  fireEvent.change(within(entrar).getByLabelText('E-mail'), {
    target: { value: 'ana@example.com' },
  })
  fireEvent.change(within(entrar).getByLabelText('Senha'), {
    target: { value: 'senha-segura' },
  })
  fireEvent.submit(entrar)

  fireEvent.click(await screen.findByRole('link', { name: 'Perfil' }))

  const formulario = await screen.findByRole('form', { name: 'Corpo' })

  fireEvent.change(within(formulario).getByLabelText('Idade'), {
    target: { value: '30' },
  })
  fireEvent.click(within(formulario).getByRole('radio', { name: 'Masculino' }))
  fireEvent.change(within(formulario).getByLabelText('Peso'), {
    target: { value: '80' },
  })
  fireEvent.change(within(formulario).getByLabelText('Altura'), {
    target: { value: '180' },
  })
  fireEvent.submit(formulario)

  expect(await screen.findByText('1780')).toBeInTheDocument()
  expect(screen.queryByText(/manuten/i)).toBeNull()
  expect(screen.queryByText(/^meta$/i)).toBeNull()

  const gravacao = vi.mocked(fetch).mock.calls.find((chamada) => (
    String(chamada[0]).endsWith('/api/perfil') && chamada[1]?.method === 'PUT'
  ))

  expect(gravacao?.[0]).toBe(`${apiUrl}/api/perfil`)
  expect(JSON.parse(gravacao[1].body)).toEqual({
    age_years: 30,
    sex: 'masculino',
    weight_kg: 80,
    height_cm: 180,
  })
  expect(gravacao[1].credentials).toBe('include')
  expect(gravacao[1].headers['X-XSRF-TOKEN']).toBe('token-de-teste')
})
