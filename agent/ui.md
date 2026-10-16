# UI

Client screens are a phone-first dark interface, built so the app can be installed on the home screen. Shadcn/ui (Radix underneath) plus Tailwind CSS draw every control. Rings and charts come from Recharts, which is the chart layer shadcn already uses. One visual system, one set of tokens.

The client shows what the API returned and sends what the person typed. Color states a meaning. It does not compute the target, the load change, or the day's calories.

Screen files stay in the context folders from [development.md](development.md): `client/src/acesso/`, `objetivo/`, `perfil/`, `registro-de-exercicio/`, `registro-de-kcal/`, and `rota/`. Shared controls live in `client/src/components/ui/`. Tokens live once in the Tailwind theme. A screen uses the token name, not a fresh hex.

Copy on the screen is Portuguese.

## 1. Depth

Surfaces step lighter as they come closer to the person. A border separates a block. A shadow does not.

| Role | Token | Hex | Use |
| --- | --- | --- | --- |
| Page | `zinc-950` | `#09090B` | The screen behind everything. |
| Surface | `zinc-900` | `#18181B` | Meal panels and exercise blocks. |
| Raised | `zinc-800` | `#27272A` | Drawers, modals, and the input while it is active. |
| Border | `zinc-800` | `#27272A` | Edge of a surface on the page. |
| Border, strong | `zinc-700` | `#3F3F46` | Edge of a raised surface, when `zinc-800` would disappear. |
| Text | `zinc-50` | `#FAFAFA` | Names, values, and actions. |
| Text, secondary | `zinc-400` | `#A1A1AA` | Units (`kg`, `kcal`, `g`) and captions. |

Load entry and adding a food open a drawer on the raised surface. The page underneath stays `zinc-950`.

## 2. Primary

The protagonist is emerald, `emerald-500` / `#10B981`. It is the brand and the single main action on a screen: iniciar treino, registrar refeição, concluir série. Focus uses the same color.

Lime, orange, and cyan stay out of the brand. Success green is a separate signal, defined below, and is never the button.

## 3. Macronutrients

Nutrition marks keep these colors on every screen. After a few days the bar color is the missing macro.

| Mark | Token | Hex |
| --- | --- | --- |
| Protein | `pink-500` | `#EC4899` |
| Carbohydrate | `amber-500` | `#F59E0B` |
| Fat | `violet-500` | `#8B5CF6` |
| Calories, center ring | `emerald-500` | `#10B981` |

The donut reads in that order: pink, amber, and violet on the ring, emerald on the calorie total in the center. These four colors do not paint buttons, errors, or completed rows.

## 4. Goal feedback

Feedback says whether the day matches the body goal. It uses its own scale.

| State | Token | Hex | When |
| --- | --- | --- | --- |
| Success | `green-500` | `#22C55E` | The set is done, or the protein target is met. The row fills with `rgba(34, 197, 94, 0.15)`. |
| Attention | `yellow-500` | `#EAB308` | The day is short by calories, or the load is below the previous session. |
| Danger | `red-500` | `#EF4444` | Intake passed the calorie ceiling on a cut. |
| Record | `amber-400` → `orange-500` | `#FBBF24` → `#F97316` | A personal load record, or a seven-day streak. The mark is the gradient `from-amber-400 to-orange-500`. |

Purple stays off this scale. It is already the fat mark.

## 5. Building a screen

Compose for a phone column first. The same column is the installed app. Keep controls at least 44px on the touch side. Respect the safe area.

1. The page is `zinc-950`. Group a meal or an exercise in a `zinc-900` card with a `zinc-800` border.
2. Put the commit action on one primary button. Other actions stay secondary, on the surface, with a border.
3. Open load and food entry in a drawer on `zinc-800`.
4. Paint a macro only with the nutrition scale. Paint a completed set, a shortfall, a ceiling break, or a record only with the goal scale.
5. Read the numbers from the API, then map them to these tokens. The bar and the ring do not recalculate the plan.
