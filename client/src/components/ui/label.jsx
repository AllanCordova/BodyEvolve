import { Root } from '@radix-ui/react-label'
import { cn } from '@/lib/utils.js'

export default function Label({ className, ...props }) {
  return <Root className={cn('text-sm font-medium text-zinc-50', className)} {...props} />
}
