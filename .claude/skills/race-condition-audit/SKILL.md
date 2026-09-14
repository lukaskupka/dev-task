---
name: race-condition-audit
description: Audit a create/update mutation's full stack (frontend form → GraphQL mutation → application command handler → repository → DB schema) for race conditions that cause duplicate or inconsistent records, and fix the root cause. Use when a bug report mentions duplicate records, double bookings, double-submits, or a race condition on a mutation. Arguments: <entity or mutation name>, e.g. "Booking" or "CreateBookingMutation".
---

# Race Condition Audit

Find and fix the *actual* cause of a "duplicate record" or "race condition" bug in this
codebase's Domain / Application / Infrastructure / UserInterface layering, rather than
papering over it with a frontend-only fix.

This skill exists because the original duplicate-bookings bug in this project was caused
by a check-then-act (TOCTOU) gap: nothing prevented two concurrent `CreateBooking`
requests from both passing validation and both inserting a row for the same slot. The
real fix was a database-level unique constraint plus translating its violation into a
typed domain error — a frontend guard alone would not have caught two requests arriving
from different tabs/devices at the same time.

## Arguments

`<entity or mutation name>` — e.g. `Booking`, `CreateBookingMutation`. Used to locate the
relevant files across layers.

## Procedure

Work through these steps in order. Report what you find at each step before moving to
the next; don't jump straight to writing a fix.

### 1. Trace the full flow

Locate every layer the mutation passes through:

- **Frontend**: the component/hook that calls the GraphQL mutation
  (`frontend/src/components/**`)
- **GraphQL mutation**: `backend/src/UserInterface/GraphQL/Mutations/**`
- **Application command handler**: `backend/src/Application/**/Command/**`
- **Repository interface + Doctrine implementation**:
  `backend/src/Domain/**/Repository/**Interface.php` and
  `backend/src/Infrastructure/**/Repository/Doctrine*.php`
- **DB schema**: `backend/migrations/**` and the Doctrine entity mapping for the
  affected table

### 2. Audit the frontend for duplicate-submission bugs

Look for anything that could fire the same mutation more than once for one user action:

- the same mutation called twice (e.g. concurrently via `Promise.all`, or from a
  double-click with no guard)
- a submit button that isn't disabled/locked while the mutation is in flight
- no ref/flag guard preventing re-entrant `handleSubmit` calls

This class of bug is real and worth fixing, but treat it as defense-in-depth, not the
root-cause fix — it only protects against *one browser tab* submitting twice.

### 3. Audit the backend for check-then-act (TOCTOU) races

This is the step most likely to reveal the real bug. Look at the command handler and
repository for a pattern like:

```
$existing = $repository->findConflicting(...);
if ($existing !== null) { throw ...; }
$repository->save($newEntity);
```

Two concurrent requests can both pass the `findConflicting` check before either has
saved — the check is not atomic with the write. If you find this pattern, or find *no*
uniqueness check at all (application-level or DB-level), that's the root cause.

Confirm by checking whether the DB schema has an atomic constraint (unique index,
partial unique index, exclusion constraint) enforcing the invariant. If it doesn't,
the check-then-act code is the only thing preventing duplicates, and it's racy by
construction.

### 4. Add the missing DB-level constraint if needed

If step 3 found no atomic constraint, write a Doctrine migration adding one that
matches the actual domain invariant (e.g. a partial unique index that excludes
cancelled/rejected rows, as in `Version20260914120000.php`). This is the only thing
that is actually safe under concurrency — everything else is best-effort.

### 5. Check exception translation stays at the infrastructure boundary

The repository's Doctrine implementation should catch the low-level DBAL exception
(`Doctrine\DBAL\Exception\UniqueConstraintViolationException`) and throw a domain
exception instead. This catch must live in
`Infrastructure/**/Repository/Doctrine*Repository.php`, **not** in the Application
command handler — the Application layer must not know Doctrine exists. If you find the
catch in the handler, move it into the repository's `save()`/`update()` method and add
a `@throws` annotation on the repository interface.

### 6. Check the domain error surfaces as a stable, typed code through the API

The GraphQL mutation should catch the specific domain exception and return a stable
machine-readable error code (see `UserErrorType`'s `code` field and
`SlotAlreadyBookedException::ERROR_CODE` for the existing pattern), not just a generic
message. The frontend should be able to switch on that code (e.g. to refresh stale data)
rather than pattern-matching error text.

### 7. Add a frontend guard as defense-in-depth

If step 2 found a real gap, fix it (disable-while-pending, submission ref/lock). State
explicitly that this is secondary to the DB constraint, not a replacement for it.

### 8. Add a regression test

Add/extend a test for the repository or command handler that asserts:

- the repository throws the domain exception when the DB constraint is violated
  (integration test against the real DB if feasible, otherwise assert the DBAL
  exception is caught and translated)
- the command handler propagates the domain exception without importing or
  referencing any Doctrine/DBAL type

## Output

Summarize findings as a short checklist, one line per step 1–8, marking each
`OK` / `FIXED` / `N/A`, followed by the list of files changed. Don't restate the whole
procedure — just the findings and the diff.
