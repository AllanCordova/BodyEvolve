import Input from '../components/ui/input.jsx'
import Label from '../components/ui/label.jsx'
import { cn } from '@/lib/utils.js'

export default function Campo({ rotulo, erro, className, ...props }) {
  return (
    <div className="flex flex-col gap-2">
      <Label className="flex flex-col gap-2">
        {rotulo}
        <Input
          aria-invalid={erro ? true : undefined}
          className={cn(erro && 'border-red-500 focus:border-red-500', className)}
          {...props}
        />
      </Label>
      {erro ? <p className="text-sm text-red-500" role="alert">{erro}</p> : null}
    </div>
  )
}
