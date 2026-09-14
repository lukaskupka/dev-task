---
name: import-candidate-repo
description: Import a developer candidate's solution from their external repository into reservio/developer-task as a feature branch. Use when given a candidate repo URL to review. Arguments: <url> <candidate-name> [branch]
---

# Import Candidate Repository

Import a candidate's developer-task solution into this repo as a `feature/{name}` branch, preserving their commit history.

## Arguments

Parse from the skill invocation arguments (`<url> <candidate-name> [branch]`):

| Argument | Required | Description |
|----------|----------|-------------|
| `url` | yes | Candidate's repository URL (HTTPS or SSH) |
| `candidate-name` | yes | Candidate name, used for branch name `feature/{candidate-name}` |
| `branch` | no | Branch in candidate's repo. If omitted, the script autodetects `main` or `master` |

## Procedure

Run the import script bundled with this skill, passing the parsed arguments:

```bash
bash .claude/skills/import-candidate-repo/import.sh {url} {candidate-name} {branch}
```

If no `branch` argument was provided, omit it — the script autodetects `main` or `master`.

### What the script does

1. Validates we are in `reservio/developer-task` with a clean working tree
2. Adds candidate's repo as a temporary remote (`candidate-{name}`)
3. Fetches and detects the correct branch
4. Creates `feature/{name}` from `origin/main`
5. Cherry-picks all candidate commits with `--reset-author`
6. Skips empty commits (e.g. initial commit that mirrors main)
7. On conflict — stops, shows status, instructs user to resolve
8. Removes the temporary remote
9. Reports results (imported/skipped counts, push command)

### After the script finishes

Report the script output to the user. If a conflict occurred, help them resolve it.
