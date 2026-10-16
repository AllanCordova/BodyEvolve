export const apiUrl = import.meta.env.VITE_API_URL ?? 'http://localhost:8000'

export async function prepararCsrf() {
  const resposta = await fetch(`${apiUrl}/sanctum/csrf-cookie`, {
    credentials: 'include',
    headers: { Accept: 'application/json' },
  })

  if (!resposta.ok) {
    throw new Error('Não foi possível preparar a sessão.')
  }
}

function tokenCsrf() {
  const cookie = document.cookie
    .split('; ')
    .find((item) => item.startsWith('XSRF-TOKEN='))

  if (!cookie) {
    return ''
  }

  return decodeURIComponent(cookie.slice('XSRF-TOKEN='.length))
}

export async function pedir(caminho, opcoes = {}) {
  const method = opcoes.method ?? 'GET'
  const headers = {
    Accept: 'application/json',
    ...opcoes.headers,
  }

  if (opcoes.body !== undefined && !(opcoes.body instanceof FormData)) {
    headers['Content-Type'] = 'application/json'
  }

  if (method !== 'GET' && method !== 'HEAD') {
    headers['X-XSRF-TOKEN'] = tokenCsrf()
  }

  const resposta = await fetch(`${apiUrl}${caminho}`, {
    ...opcoes,
    method,
    headers,
    credentials: 'include',
  })

  const texto = await resposta.text()
  const corpo = texto ? JSON.parse(texto) : null

  if (!resposta.ok) {
    const erro = new Error(corpo?.message ?? 'Falha na requisição')
    erro.status = resposta.status
    erro.corpo = corpo
    throw erro
  }

  return corpo
}

export async function sessaoAtual() {
  try {
    return await pedir('/api/acesso/sessao')
  } catch (erro) {
    if (erro.status === 401) {
      return null
    }

    throw erro
  }
}

export function criarConta(dados) {
  return enviar('/api/acesso/conta', dados)
}

export function entrar(credenciais) {
  return enviar('/api/acesso/sessao', credenciais)
}

export async function sair() {
  await prepararCsrf()

  return pedir('/api/acesso/sessao', { method: 'DELETE' })
}

async function enviar(caminho, credenciais) {
  await prepararCsrf()

  return pedir(caminho, {
    method: 'POST',
    body: JSON.stringify(credenciais),
  })
}
