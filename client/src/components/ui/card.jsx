import { cn } from '@/lib/utils.js'

export default function Card({ className, ...props }) {
  return (
    <div
      className={cn('rounded-lg border border-zinc-800 bg-zinc-900 p-4 text-zinc-50', className)}
      {...props}
    />
  )
}
