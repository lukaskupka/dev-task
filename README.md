# Developer Task – Barbershop Booking System

- [What to do](#what-to-do) — Main Task, Bonus Task, Submission
- [Project Overview](#project-overview) — Architecture, Getting Started, Using the App
- [Discussion Questions](#discussion-questions) — Interview only, not part of submission

## What to do

### Main Task

**Goal:** Find and fix the bug that causes duplicate bookings on the backend.

**Context:** Customers are reporting that after submitting a booking they sometimes receive two confirmations for the same time slot. In the administration panel we can see two identical bookings — same customer, same stylist, same time. It doesn't happen every time.

**Scope:**
- Investigate the root cause
- Propose and implement a fix on the backend
- We recommend using AI tools while working on this task

### Bonus Task

Create a Claude Code skill that solves a problem you actually hit while working on the main task. The skill must be functional and usable in the Claude Code CLI.

### Submission

Push your solution to a repository on any code hosting platform (GitHub, GitLab, Bitbucket, etc.) and share the link with us.

---

## Project Overview

A simple online booking application for two barbershops. Customers can choose a service, a stylist, a time slot, and submit a reservation.

### Architecture

- **Backend** – PHP application (Nette Framework, Doctrine ORM, GraphQL API), SQLite database
- **Frontend** – Next.js (React, Apollo Client, Tailwind CSS)
- Communication goes through a GraphQL endpoint on the backend

### Getting Started

#### Requirements

- Docker + Docker Compose

#### Steps

```bash
# 1. Start the containers (env files are created automatically)
make up

# 2. Run database migrations and load test data
make db-reset
```

The default configuration uses `docker-compose.local.yml`, which maps ports to localhost. Once running:

- **Frontend** at [http://localhost:3000](http://localhost:3000)
- **GraphQL API** at [http://localhost:8080/graphql](http://localhost:8080/graphql)

### Using the App

At [http://localhost:3000](http://localhost:3000) you'll find a list of available barbershops. Click on one to open its detail page, where you can:

1. Select a service (haircut, shave, …)
2. Choose a stylist and a date
3. Click an available time slot
4. Fill in your name and contact (email or phone) and submit with the **Book** button

### Booking Administration

The business panel is available at [http://localhost:3000/business-panel](http://localhost:3000/business-panel).

Credentials:

| Field    | Value    |
|----------|----------|
| Username | `admin`  |
| Password | `barber` |

After logging in you'll see all bookings grouped by date. You can switch between barbershops using the tabs at the top. For each booking in **Pending** status you can:

- **Confirm** – confirm the booking
- **Reject** – reject the booking

Confirmed and rejected bookings display their status and no further actions are available.

### Useful Commands

```bash
make up          # build and start containers
make down        # stop and remove containers
make db-reset    # run migrations and load fixtures
make fixtures    # load fixtures only (clears existing data)
make bash        # open a shell in the backend container
make logs        # tail backend container logs
```

---

## Discussion Questions

The following questions are **not part of the submission** — we'll go through them together during the interview. You are welcome and encouraged to bring your notes — on paper, phone, tablet, laptop, whatever works for you.

1. Walk us through your solution — how you found the root cause, what you tried, and why you fixed it the way you did.
2. Did you notice anything unusual or non-standard in the project? Name at least three things and how you would approach them differently.
3. How has AI changed your approach to software development over the last year? What do you find most useful about it, and what risks do you see?
