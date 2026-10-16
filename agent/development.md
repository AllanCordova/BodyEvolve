# Development

The user picks one GitHub issue. The agent creates the branch, writes a plan, and waits. Product code starts only after the user approves the plan. Before anything is pushed, the agent runs the same checks as CI.

Issues come from [tasks.md](tasks.md). Branch names, commits, and pull requests follow [git.md](git.md).

## 1. Choose the issue

The user names the issue. Read it before naming the branch:

```bash
gh issue view <issue-number>
```

The issue title carries the story id (`US-01: Enter the platform`). The branch uses the GitHub issue number, not the story id.

## 2. Create the branch

Start from an updated `main`, then create the branch in the form from [git.md](git.md):

```bash
git checkout main
git pull origin main
git checkout -b <type>/<issue-number>-<short-description>
```

A user story uses `feat/`. The description is lowercase kebab-case, taken from the issue title, without the `US-` id.

Issue `#12`, titled `US-01: Enter the platform`, becomes:

```bash
git checkout -b feat/12-enter-the-platform
```

## 3. Write the plan

Write the plan before changing product code. It is the message the user reviews. It has three required parts, in this order.

```markdown
## Acceptance criteria
<!-- What must be true for this issue to be done. Taken from the issue, not invented. -->
-

## Tests
<!-- The automated tests that will prove those criteria. Name the file and what it asserts. -->
-

## Solution
<!-- A short description of how the solution will be built, and the paths where it will live. -->
```

**Acceptance criteria.** Restate the issue's acceptance points as checkable outcomes for this change.

**Tests.** Name the test files and what each one asserts. API rules go in `api/tests/<Context>/` and run with Pest. Client behavior goes in `client/src/<context>/` and runs with Vitest. The client test checks that the screen sends what the person typed and shows what the API returned. It does not repeat a calculation that belongs to the API.

**Solution.** Say how the change will be built, in a few sentences, and where the files will sit:

- API behavior for a context goes in `api/app/<Context>/`. HTTP entry points in that folder carry the OpenAPI attributes.
- Routes, config, and migrations stay in the Laravel shell, outside those context names.
- The client screen for that context goes in `client/src/acesso/`, `objetivo/`, `perfil/`, `registro-de-exercicio/`, `registro-de-kcal/`, or `rota/`.

## 4. Approval

Stop after the plan. Do not add product code, tests, or commits for the solution while the plan is open.

The user either approves it or asks for changes. On a change request, revise the plan and stop again. Implementation starts from the approved plan only.

## 5. Local CI

When the approved work is in the tree, run the checks from [.github/workflows/ci-cd.yml](../.github/workflows/ci-cd.yml) on the machine. Do not push until all four finish with a passing status.

API, from `api/`:

```bash
composer install --no-interaction --prefer-dist
vendor/bin/pint --test
vendor/bin/pest
```

Client, from `client/`:

```bash
npm ci
npm run lint
npm test
```

`npm run lint` is ESLint. `npm test` is Vitest, once, not in watch mode.

On CI, Pest uses Postgres 18 with `DB_CONNECTION=pgsql`, `DB_HOST=127.0.0.1`, `DB_PORT=5432`, `DB_DATABASE=bodyevolve`, `DB_USERNAME=bodyevolve`, and `DB_PASSWORD=bodyevolve`. For a change that reads or writes the database, export those variables and run Pest against the Postgres from `compose.local.yml`, so the local run matches the API job.

If a command fails, fix the cause and run the full set again. Then commit, push, and open the pull request as in [git.md](git.md), with `Resolves #<issue-number>` in the related-issue section.
