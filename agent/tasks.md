# Tasks

A task is a GitHub issue. Each issue is generated from one user story in [docs/system/backlog.md](../docs/system/backlog.md). The issue number is the task id that [git.md](git.md) uses in the branch name and in the pull request.

The backlog stays the source of the rules. The issue is the unit of work that gets a branch and a pull request.

## Stories

Create one issue per story. Do not open the branch from the story id alone.

| Story | Issue title |
| --- | --- |
| US-01 | US-01: Enter the platform |
| US-02 | US-02: Record the body |
| US-03 | US-03: Choose one goal |
| US-04 | US-04: State how active the day is |
| US-05 | US-05: See maintenance calories |
| US-06 | US-06: See the goal target |
| US-07 | US-07: See the micronutrient focus |
| US-08 | US-08: Log cardio or sport |
| US-09 | US-09: Log a strength session |
| US-10 | US-10: See whether the load went up |
| US-11 | US-11: Record the day's food |
| US-12 | US-12: Know whether the day is on the route |

## Issue body

Write the issue in English. Copy the story's intent and acceptance points from the backlog. Link the story so the original text stays the authority.

```markdown
## Story
US-01 in docs/system/backlog.md

## Intent
As a person who owns their own data, I want to sign in with email and password, so that I only see my plan, my training, and my food.

## Acceptance
- I create the account with email and password.
- I sign in with those credentials.
- Signing out ends access.
- Without a session, profile, training, food, and alerts do not open.
- What I log stays on the account I signed in with.

## Source
docs/system/backlog.md (US-01)
```

## Create the issue

From the repository root, with the `gh` CLI authenticated:

```bash
gh issue create --title "US-01: Enter the platform" --body "$(cat <<'EOF'
## Story
US-01 in docs/system/backlog.md

## Intent
As a person who owns their own data, I want to sign in with email and password, so that I only see my plan, my training, and my food.

## Acceptance
- I create the account with email and password.
- I sign in with those credentials.
- Signing out ends access.
- Without a session, profile, training, food, and alerts do not open.
- What I log stays on the account I signed in with.

## Source
docs/system/backlog.md (US-01)
EOF
)"
```

Note the number `gh` prints. That number is the task id from here on. If the story is already an open issue, use that issue. Do not open a second one.

## Link the issue to the git workflow

[git.md](git.md) consumes the issue number. Building the issue follows [development.md](development.md). For issue `#12` of US-01:

1. Start from an updated `main`, as in git.md section 1.
2. Name the branch `feat/12-enter-the-platform`. The number is `12`, not `US-01`.
3. Open the pull request with the template in git.md section 4.
4. In **Related task / issue**, write `Resolves #12`.

Merging that pull request closes the issue. The story id stays in the issue title, so the backlog and the issue remain tied after the number is assigned.
