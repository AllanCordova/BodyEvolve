import { ChevronLeft } from 'lucide-react'
import { Link } from 'react-router'

export default function Voltar() {
  return (
    <Link to="/" className="inline-flex min-h-11 w-fit items-center gap-1 text-sm text-zinc-400">
      <ChevronLeft className="size-4" aria-hidden="true" />
      Início
    </Link>
  )
}
