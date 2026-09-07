<p align="right"><a href="README.en.md">🇬🇧 Read in English</a></p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Vite-7-646CFF?logo=vite&logoColor=white" alt="Vite 7">
  <img src="https://img.shields.io/badge/Tailwind-4-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/Moodle-integrado-F98012?logo=moodle&logoColor=white" alt="Moodle integrado">
  <img src="https://img.shields.io/badge/i18n-EN%20%2F%20ES-blueviolet" alt="i18n EN/ES">
</p>

<h1 align="center">PairSync</h1>
<p align="center"><em>Code together, think together.</em></p>
<p align="center">Plataforma de aprendizaje colaborativo de programación: cursos, retos, sesiones de <strong>pair programming</strong> en tiempo real y calificaciones sincronizadas con <strong>Moodle</strong>.</p>

---

## Descripción

**PairSync** nació como una herramienta de sesiones de *pair programming* remotas y creció hasta convertirse en una pequeña plataforma educativa: los profesores crean cursos y lecciones, los estudiantes se inscriben y practican en salas colaborativas de código en tiempo real (con roles Driver/Navigator y un tutor de IA integrado en el chat), y el profesor califica cada lección y **sincroniza la nota directamente en Moodle**, sin salir de la aplicación.

Es un **proyecto de grado**, pensado originalmente para el aprendizaje colaborativo de Android/Kotlin.

> Nota para curiosos: pese a las apariencias, el frontend **no usa React** — es Blade (renderizado en servidor) + JavaScript plano, con Tailwind CSS para estilos.

## Características

- **Autenticación y roles** — login/registro con dos roles: `profesor` y `estudiante`.
- **Cursos y lecciones** — los profesores crean cursos con lecciones ordenadas y contenido propio; los estudiantes se inscriben o se vinculan a un profesor mediante un código.
- **Catálogo de retos** — vista de cursos/retos disponibles para que el estudiante explore y se inscriba.
- **Calificaciones sincronizadas con Moodle** — el profesor califica cada lección por estudiante (escala 0–5, con retroalimentación) desde un tablero propio; con un clic, la nota se envía a Moodle a través de su API REST (`mod_assign_save_grades`), emparejando al alumno por correo electrónico. Soporta sincronización individual o masiva por curso.
- **Salas por código** — se genera un código único de 6 caracteres al crear una sesión de pareja; el compañero se une con ese código.
- **Roles Driver / Navigator** — el creador de la sala es el Driver (escribe el código) y quien se une es el Navigator (guía la estrategia); los roles se pueden intercambiar (`swap`) durante la sesión.
- **Sincronización en tiempo real** — *polling* cada 2 segundos mantiene a ambos dispositivos al día sobre el estado de la sesión, los roles y el historial del chat.
- **Chat con tutor de IA** — un asistente conversacional (*Android Kotlin Tutor*) integrado vía [LangGraph](https://www.langchain.com/langgraph), con respuestas en streaming (SSE) y renderizado básico de Markdown/código.
- **Historial persistente** — el hilo de conversación y los mensajes del chat se guardan en base de datos, para que ambos participantes vean el mismo historial al recargar o reconectarse.
- **Internacionalización** — interfaz disponible en inglés y español (`lang/es.json`), con selector de idioma persistido en sesión.

## Cómo funciona

**Flujo del profesor**
1. Crea un curso y sus lecciones (cada lección puede vincularse a un *assignment* de Moodle).
2. Comparte con sus estudiantes su código de profesor para que se vinculen.
3. Entra al tablero de calificaciones, califica lección por lección y sincroniza las notas a Moodle.

**Flujo del estudiante**
1. Se inscribe en un curso o se vincula a su profesor con el código.
2. Crea o se une a una sala de pair programming (`/session/create` o `/session/join`) usando un código de 6 caracteres.
3. Dentro de la sala (`/room/{code}`) practica con su compañero — con roles intercambiables y un tutor de IA disponible en el chat — mientras todo el estado (roles, hilo de LangGraph, historial de chat) se persiste en base de datos.

## Stack técnico

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Blade (SSR) + JavaScript plano, Tailwind CSS 4 |
| Build | Vite 7 |
| Base de datos | SQLite (por defecto, configurable a MySQL/PostgreSQL) |
| IA conversacional | LangGraph (agente externo vía API) |
| LMS | Moodle (API REST de Web Services) |

## Requisitos

- PHP >= 8.2
- Composer
- Node.js + npm
- Una instancia de agente LangGraph (URL, API key y `agent_id`) para el chat con IA
- Un sitio Moodle con Web Services habilitados y un token con permisos sobre `mod_assign_save_grades`, `core_user_get_users_by_field` y `core_enrol_get_enrolled_users`, para la sincronización de notas

## Instalación

```bash
git clone https://github.com/cperez233/PairProgramming.git
cd PairProgramming
composer install
npm install
```

Configura el entorno:

```bash
cp .env.example .env
php artisan key:generate
```

Agrega las credenciales de LangGraph y Moodle a tu `.env`:

```env
LANGRAPH_API_URL=
LANGRAPH_API_KEY=
LANGRAPH_AGENT_ID=

MOODLE_URL=
MOODLE_WS_TOKEN=
MOODLE_COURSE_ID=
```

Ejecuta las migraciones (por defecto usa SQLite):

```bash
touch database/database.sqlite
php artisan migrate
```

## Uso en desarrollo

```bash
composer run dev
```

Este comando levanta en paralelo el servidor de Laravel, el listener de colas, los logs (`pail`) y Vite. La app queda disponible en `http://localhost:8000`.

## Estructura relevante

```
app/Http/Controllers/SessionController.php   # Creación/unión de salas, roles, chat
app/Http/Controllers/CourseController.php    # Gestión de cursos (profesor)
app/Http/Controllers/LessonController.php    # Gestión de lecciones
app/Http/Controllers/ChallengeController.php # Catálogo de retos, inscripción, vínculo con profesor
app/Http/Controllers/GradeController.php     # Tablero de calificaciones + sync con Moodle
app/Services/MoodleService.php               # Cliente de la API REST de Moodle
app/Models/                                  # User, Course, Lesson, Grade, PairSession
database/migrations/                         # Esquema completo (usuarios, cursos, lecciones, grades, sesiones)
resources/views/                             # Vistas de auth, cursos, retos, calificaciones y salas
lang/es.json                                 # Traducciones al español
```

## Autores

Proyecto de grado desarrollado por:

- **Cristian Pérez** ([@cperez233](https://github.com/cperez233))
- **Jorge Vergel** ([@jorgev898](https://github.com/jorgev898))
