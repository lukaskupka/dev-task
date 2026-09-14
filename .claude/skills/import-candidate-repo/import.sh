#!/usr/bin/env bash
set -euo pipefail

# Usage: ./import.sh <repo-url> <candidate-name> [branch]

if [[ $# -lt 2 ]]; then
  echo "Usage: $0 <repo-url> <candidate-name> [branch]"
  exit 1
fi

REPO_URL="$1"
CANDIDATE="$2"
BRANCH="${3:-}"
# --- Validate preconditions ---
ORIGIN=$(git remote get-url origin 2>/dev/null || true)
if [[ "$ORIGIN" != *"reservio/developer-task"* ]]; then
  echo "ERROR: This must be run from the reservio/developer-task repository."
  echo "Current origin: $ORIGIN"
  exit 1
fi

if [[ -n $(git status --porcelain) ]]; then
  echo "ERROR: Working tree is not clean. Commit or stash your changes first."
  exit 1
fi

# --- Create feature branch ---
FEATURE_BRANCH="feature/${CANDIDATE}"
if git rev-parse --verify "$FEATURE_BRANCH" &>/dev/null; then
  echo "ERROR: Branch $FEATURE_BRANCH already exists."
  echo "Delete it manually (git branch -D $FEATURE_BRANCH) and re-run, or abort."
  exit 1
fi

# --- Fetch directly by URL (no remote needed, avoids .git/config writes) ---
echo "Fetching from $REPO_URL ..."

# Detect branch: try fetching main, then master, then user-specified
if [[ -n "$BRANCH" ]]; then
  if ! git fetch "$REPO_URL" "$BRANCH" 2>&1; then
    echo "ERROR: Failed to fetch branch '$BRANCH' from $REPO_URL"
    exit 1
  fi
else
  if git fetch "$REPO_URL" main 2>/dev/null; then
    BRANCH="main"
  elif git fetch "$REPO_URL" master 2>/dev/null; then
    BRANCH="master"
  else
    echo "ERROR: Could not fetch main or master branch from $REPO_URL"
    echo "Specify the branch explicitly as the third argument."
    exit 1
  fi
fi

echo "Using branch: $BRANCH"

# FETCH_HEAD now points to the tip of the candidate's branch
CANDIDATE_HEAD=$(git rev-parse FETCH_HEAD)

git checkout -b "$FEATURE_BRANCH" origin/main

# --- Import commits by copying files directly from candidate's tree ---
COMMITS=$(git rev-list --reverse origin/main.."$CANDIDATE_HEAD")

if [[ -z "$COMMITS" ]]; then
  echo "No commits to import."
  git checkout -
  git branch -D "$FEATURE_BRANCH"
  exit 1
fi

TOTAL=$(echo "$COMMITS" | wc -l | tr -d ' ')
IMPORTED=0
SKIPPED=0

echo "Found $TOTAL commits to import..."

for SHA in $COMMITS; do
  MSG=$(git log --format='%s' -1 "$SHA")

  # Skip root commits (no parent) — these are just the initial template setup
  if ! git rev-parse --verify "${SHA}^" &>/dev/null; then
    SKIPPED=$((SKIPPED + 1))
    echo "  [skip/$TOTAL] $MSG (root commit)"
    continue
  fi

  PARENT=$(git rev-parse "${SHA}^")

  # Get files added/modified and deleted in this commit
  ADDED_MODIFIED=$(git diff --name-only --diff-filter=ACMR "$PARENT" "$SHA")
  DELETED=$(git diff --name-only --diff-filter=D "$PARENT" "$SHA")

  if [[ -z "$ADDED_MODIFIED" ]] && [[ -z "$DELETED" ]]; then
    SKIPPED=$((SKIPPED + 1))
    echo "  [skip/$TOTAL] $MSG (no file changes)"
    continue
  fi

  # Copy added/modified files directly from the candidate's commit
  if [[ -n "$ADDED_MODIFIED" ]]; then
    while IFS= read -r FILE; do
      mkdir -p "$(dirname "$FILE")"
      git show "${SHA}:${FILE}" > "$FILE"
      git add -f "$FILE"
    done <<< "$ADDED_MODIFIED"
  fi

  # Remove deleted files
  if [[ -n "$DELETED" ]]; then
    while IFS= read -r FILE; do
      git rm -f "$FILE" 2>/dev/null || true
    done <<< "$DELETED"
  fi

  # Commit if there are staged changes
  if [[ -n $(git diff --cached --name-only) ]]; then
    git commit -m "$MSG"
    IMPORTED=$((IMPORTED + 1))
    echo "  [$IMPORTED/$TOTAL] $MSG"
  else
    SKIPPED=$((SKIPPED + 1))
    echo "  [skip/$TOTAL] $MSG (no effective changes)"
  fi
done

# --- Push and create PR ---
echo ""
echo "Pushing $FEATURE_BRANCH to origin..."
git push -u origin "$FEATURE_BRANCH"

# Build PR title: capitalize candidate name parts
PR_TITLE="$(echo "$CANDIDATE" | sed 's/-/ /g' | sed 's/\b\(.\)/\u\1/g'): Developer task"

echo "Creating pull request..."
PR_BODY="Source repository: $REPO_URL"
PR_URL=$(gh pr create --base main --title "$PR_TITLE" --body "$PR_BODY" 2>&1)
echo "PR created: $PR_URL"

# --- Report ---
echo ""
echo "=== Import complete ==="
echo "Branch:   $FEATURE_BRANCH"
echo "Imported: $IMPORTED commits"
echo "Skipped:  $SKIPPED commits (empty)"
echo "PR:       $PR_URL"
