import { cn } from '@/lib/utils.js'

export default function Input({ className, ...props }) {
  return (
    <input
      className={cn(
        'h-11 w-full rounded-md border border-zinc-800 bg-zinc-950 px-3 text-base text-zinc-50 outline-none placeholder:text-zinc-400 focus:border-zinc-700 focus:bg-zinc-800 focus-visible:ring-2 focus-visible:ring-emerald-500',
        className,
      )}
      {...props}
    />
  )
}
