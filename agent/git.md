# Git Standards, Workflow, and Pull Requests

This document defines the required rules for branches, commit messages, and pull requests. Developers and automated agents follow them so the repository history stays clean, predictable, and free of conflicts.

Task ids used below are GitHub issue numbers. Issues are created from user stories as described in [tasks.md](tasks.md).

---

## 1. Workflow rule

**Never** create a new branch from an old or stale working branch. Every new task starts from the latest state of the default branch (`main` or `master`).

### Required steps

1. **Switch to the default branch:**
   ```bash
   git checkout main
   ```
2. **Update the local branch from the remote:**
   ```bash
   git pull origin main
   ```
3. **Create and switch to the new branch, using the naming rule:**
   ```bash
   git checkout -b <type>/<name>
   ```
4. **Change the files, stage them, and commit in the required format:**
   ```bash
   git add .
   git commit -m "<type>(<optional-scope>): <short description>"
   ```
5. **Push the branch to the remote:**
   ```bash
   git push -u origin <type>/<name>
   ```

---

## 2. Branch names

Branch names are **lowercase**, words separated by **hyphens** (kebab-case), with no spaces or special characters.

**Format:** `<type>/<short-description>` or `<type>/<issue-number>-<short-description>`

Use the second form when the work comes from a GitHub issue. The number is the issue created in [tasks.md](tasks.md), not the user-story id.

### Allowed types

* **`feat/`**: A new capability.
  * *Example:* `feat/jwt-authentication` or `feat/104-product-filter`
* **`fix/`**: A bug found in development or staging.
  * *Example:* `fix/shipping-calculation-error`
* **`hotfix/`**: An urgent production fix.
  * *Example:* `hotfix/payment-service-down`
* **`docs/`**: Documentation only (README, guides, comments).
  * *Example:* `docs/update-git-rules`
* **`refactor/`**: A code change that does not change external behavior and does not fix a bug (cleanup, performance, structure).
  * *Example:* `refactor/simplify-id-validator`
* **`chore/`**: Maintenance, dependency updates, CI/CD, or build configuration.
  * *Example:* `chore/update-node-dependencies`
* **`test/`**: Adding or fixing automated tests only.
  * *Example:* `test/user-service-unit-tests`

---

## 3. Commit messages (Conventional Commits)

Commit messages are clear, direct, and written in the **imperative** mood (for example, "add" rather than "added" or "adds").

**Format:** `<type>(<optional-scope>): <concise description>`

### Commit rules

1. **Type:** The same prefixes as branches (`feat`, `fix`, `docs`, `refactor`, `chore`, `test`, `style`, `perf`).
2. **Scope (optional):** The module, file, or part of the system, in parentheses.
3. **Description:** A lowercase letter right after the colon and space, no trailing period, at most 72 characters.

### Good examples

* `feat(auth): add Google sign-in support`
* `fix(cart): correct the sum of duplicate items`
* `docs(readme): include environment variable instructions`
* `refactor(api): extract validation logic into middleware`
* `chore(deps): update axios to v1.6`

---

## 4. Pull request template

When opening a pull request, by hand or with an agent, fill this structure in the pull request body:

```markdown
## Description
<!-- What this pull request does, and why the change is needed. -->

## Related task / issue
<!-- Link or id from tasks.md (for example, Resolves #123). If there is no issue, write "N/A". -->
- Issue: Resolves #

## Change type
<!-- Mark with an 'x' only the options that apply, with no spaces inside the brackets: [x]. -->
- [ ] `feat`: New capability
- [ ] `fix`: Bug fix
- [ ] `hotfix`: Critical production fix
- [ ] `refactor`: Code restructure with no behavior change
- [ ] `docs`: Documentation update
- [ ] `chore`: Configuration, build, or dependency update
- [ ] `test`: Adding or fixing tests

## What changed? (technical summary)
<!-- The main files, components, or rules that changed. -->
-
-
-

## How to test
<!-- Exact steps a reviewer can follow to validate the change. -->
1. Check out this branch
2. Run `...`
3. Open the route or screen `...`
4. Confirm that `...`

## Quality checklist (required)
- [ ] The branch was created from an updated default branch (`main`/`master`) after `git pull`.
- [ ] The branch name follows `<type>/<description>` or `<type>/<issue-number>-<description>`.
- [ ] Commits follow Conventional Commits.
- [ ] The code builds and runs locally with no new errors or warnings.
- [ ] Local tests were run and passed.
- [ ] No credentials, API keys, or leftover `console.log` calls are in the code.
```

`Resolves #<issue-number>` closes that issue when the pull request merges. The issue is the one opened from the user story in [tasks.md](tasks.md).

---

## 5. Rules for AI agents

If you are an AI agent working in this repository, follow this protocol:

1. **Issue first:** Do not start the branch until the user story has a GitHub issue, created as described in [tasks.md](tasks.md). Implementation of that issue follows [development.md](development.md): plan, approval, then code.
2. **Initial check:** Before editing files, run `git status` and `git branch`. Do not commit directly on `main` or `master`.
3. **Sync:** Run `git checkout main && git pull origin main` before `git checkout -b <type>/<issue-number>-<short-description>`.
4. **Atomic commits:** Put separate logical changes in separate commits, using `<type>(<scope>): <description>`.
5. **Open the pull request:** Use section **4. Pull request template** for the body. Mark the matching checkboxes with `[x]`, list the changed files in the technical summary, and set the related issue to `Resolves #<issue-number>`.
