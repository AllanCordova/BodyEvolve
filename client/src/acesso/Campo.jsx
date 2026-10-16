import Input from '../components/ui/input.jsx'
import Label from '../components/ui/label.jsx'
import { cn } from '@/lib/utils.js'

export default function Campo({ rotulo, unidade, erro, className, name, id, ...props }) {
  const campoId = id ?? name

  return (
    <div className="flex flex-col gap-2">
      <div className="flex items-baseline justify-between gap-3">
        <Label htmlFor={campoId}>{rotulo}</Label>
        {unidade ? <span className="text-sm font-normal text-zinc-400">{unidade}</span> : null}
      </div>
      <Input
        id={campoId}
        name={name}
        aria-invalid={erro ? true : undefined}
        className={cn(erro && 'border-red-500 focus:border-red-500', className)}
        {...props}
      />
      {erro ? <p className="text-sm text-red-500" role="alert">{erro}</p> : null}
    </div>
  )
}
