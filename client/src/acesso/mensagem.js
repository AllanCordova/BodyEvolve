export function errosDoCampo(erro) {
  const erros = erro?.corpo?.errors

  if (!erros || typeof erros !== 'object') {
    return {}
  }

  return Object.fromEntries(
    Object.entries(erros).flatMap(([campo, mensagens]) => {
      const texto = Array.isArray(mensagens) ? mensagens.filter(Boolean).join(' ') : ''

      return texto ? [[campo, texto]] : []
    }),
  )
}

export function mensagem(erro) {
  const erros = erro?.corpo?.errors

  if (erros) {
    return Object.values(erros).flat().join(' ')
  }

  return erro instanceof Error ? erro.message : 'Não foi possível concluir o acesso.'
}
