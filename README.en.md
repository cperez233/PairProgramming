<p align="right"><a href="README.md">🇪🇸 Leer en Español</a></p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Vite-7-646CFF?logo=vite&logoColor=white" alt="Vite 7">
  <img src="https://img.shields.io/badge/Tailwind-4-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/Moodle-integrated-F98012?logo=moodle&logoColor=white" alt="Moodle integrated">
  <img src="https://img.shields.io/badge/i18n-EN%20%2F%20ES-blueviolet" alt="i18n EN/ES">
</p>

<h1 align="center">PairSync</h1>
<p align="center"><em>Code together, think together.</em></p>
<p align="center">A collaborative programming learning platform: courses, challenges, real-time <strong>pair programming</strong> sessions, and grades synced straight to <strong>Moodle</strong>.</p>

---

## Overview

**PairSync** started as a remote pair-programming session tool and grew into a small educational platform: teachers create courses and lessons, students enroll and practice in real-time collaborative coding rooms (with swappable Driver/Navigator roles and an AI tutor built into the chat), and the teacher grades each lesson and **syncs the grade straight to Moodle** without leaving the app.

It's a **thesis/capstone project**, originally built around collaborative Android/Kotlin learning.

> For the curious: despite appearances, the frontend does **not** use React — it's server-rendered Blade + vanilla JavaScript, styled with Tailwind CSS.

## Features

- **Authentication and roles** — login/registration with two roles: `teacher` and `student`.
- **Courses and lessons** — teachers create courses with ordered lessons and their own content; students enroll or link to a teacher via a code.
- **Challenge catalog** — a browsable view of available courses/challenges for students to explore and join.
- **Grades synced with Moodle** — the teacher grades each lesson per student (0–5 scale, with feedback) from a dedicated dashboard; one click sends the grade to Moodle through its REST API (`mod_assign_save_grades`), matching the student by email. Supports syncing a single grade or an entire course in bulk.
- **Code-based rooms** — a unique 6-character code is generated when a pair session is created; the partner joins with that code.
- **Driver / Navigator roles** — the room creator is the Driver (writes the code) and whoever joins is the Navigator (guides the strategy); roles can be swapped during the session.
- **Real-time sync** — polling every 2 seconds keeps both devices up to date on session state, roles, and chat history.
- **AI tutor chat** — a conversational assistant (*Android Kotlin Tutor*) integrated via [LangGraph](https://www.langchain.com/langgraph), with streamed (SSE) responses and basic Markdown/code rendering.
- **Persistent history** — the conversation thread and chat messages are stored in the database, so both participants see the same history on reload or reconnect.
- **Internationalization** — UI available in English and Spanish (`lang/es.json`), with the language choice persisted in session.

## How it works

**Teacher flow**
1. Creates a course and its lessons (each lesson can be linked to a Moodle assignment).
2. Shares their teacher code with students so they can link to them.
3. Opens the grading dashboard, grades lesson by lesson, and syncs grades to Moodle.

**Student flow**
1. Enrolls in a course or links to their teacher with the code.
2. Creates or joins a pair-programming room (`/session/create` or `/session/join`) using a 6-character code.
3. Inside the room (`/room/{code}`) practices with their partner — swapping roles and chatting with the AI tutor — while all state (roles, LangGraph thread, chat history) persists in the database.

## Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Blade (SSR) + vanilla JavaScript, Tailwind CSS 4 |
| Build | Vite 7 |
| Database | SQLite (default, configurable to MySQL/PostgreSQL) |
| Conversational AI | LangGraph (external agent via API) |
| LMS | Moodle (Web Services REST API) |

## Requirements

- PHP >= 8.2
- Composer
- Node.js + npm
- A LangGraph agent instance (URL, API key, and `agent_id`) for the AI chat
- A Moodle site with Web Services enabled and a token authorized for `mod_assign_save_grades`, `core_user_get_users_by_field`, and `core_enrol_get_enrolled_users`, for grade syncing

## Installation

```bash
git clone https://github.com/cperez233/PairProgramming.git
cd PairProgramming
composer install
npm install
```

Set up the environment:

```bash
cp .env.example .env
php artisan key:generate
```

Add your LangGraph and Moodle credentials to `.env`:

```env
LANGRAPH_API_URL=
LANGRAPH_API_KEY=
LANGRAPH_AGENT_ID=

MOODLE_URL=
MOODLE_WS_TOKEN=
MOODLE_COURSE_ID=
```

Run the migrations (uses SQLite by default):

```bash
touch database/database.sqlite
php artisan migrate
```

## Development

```bash
composer run dev
```

This runs the Laravel server, the queue listener, logs (`pail`), and Vite in parallel. The app is available at `http://localhost:8000`.

## Relevant structure

```
app/Http/Controllers/SessionController.php   # Room creation/joining, roles, chat
app/Http/Controllers/CourseController.php    # Course management (teacher)
app/Http/Controllers/LessonController.php    # Lesson management
app/Http/Controllers/ChallengeController.php # Challenge catalog, enrollment, teacher linking
app/Http/Controllers/GradeController.php     # Grading dashboard + Moodle sync
app/Services/MoodleService.php               # Moodle REST API client
app/Models/                                  # User, Course, Lesson, Grade, PairSession
database/migrations/                         # Full schema (users, courses, lessons, grades, sessions)
resources/views/                             # Auth, courses, challenges, grades, and room views
lang/es.json                                 # Spanish translations
```

## Authors

Thesis/capstone project developed by:

- **Cristian Pérez** ([@cperez233](https://github.com/cperez233))
- **Jorge Vergel** ([@jorgev898](https://github.com/jorgev898))
