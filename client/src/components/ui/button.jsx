import { Slot } from '@radix-ui/react-slot'
import { cva } from 'class-variance-authority'
import { cn } from '@/lib/utils.js'

const buttonVariants = cva(
  'inline-flex h-11 w-full items-center justify-center rounded-md px-4 text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 disabled:pointer-events-none disabled:opacity-50',
  {
    variants: {
      variant: {
        default: 'bg-primary text-primary-foreground hover:bg-primary/90',
        outline: 'border border-zinc-800 bg-transparent text-zinc-50 hover:bg-zinc-800',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
)

export default function Button({ className, variant, asChild = false, ...props }) {
  const Comp = asChild ? Slot : 'button'

  return <Comp className={cn(buttonVariants({ variant }), className)} {...props} />
}
