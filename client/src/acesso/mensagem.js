export function mensagem(erro) {
  const erros = erro?.corpo?.errors

  if (erros) {
    return Object.values(erros).flat().join(' ')
  }

  return erro instanceof Error ? erro.message : 'Não foi possível concluir o acesso.'
}
