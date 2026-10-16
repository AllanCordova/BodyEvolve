export default function Escolha({ valor, rotulo, detalhe, ...campo }) {
  return (
    <label className="group flex h-full min-h-11 cursor-pointer items-start gap-3 rounded-lg border border-zinc-800 px-3 py-3 has-checked:border-emerald-500 has-checked:bg-zinc-800 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-emerald-500">
      <input
        type="radio"
        className="sr-only"
        {...campo}
        value={valor}
        aria-label={rotulo}
      />
      <span
        aria-hidden="true"
        className="mt-0.5 size-4 shrink-0 rounded-full border border-zinc-600 group-has-checked:border-emerald-500 group-has-checked:bg-emerald-500"
      />
      <span className="flex min-w-0 flex-col gap-0.5">
        <span className="text-sm font-medium">{rotulo}</span>
        {detalhe ? <span className="text-sm font-normal text-zinc-400">{detalhe}</span> : null}
      </span>
    </label>
  )
}
