<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Vite-7-646CFF?logo=vite&logoColor=white" alt="Vite 7">
  <img src="https://img.shields.io/badge/Tailwind-4-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/i18n-EN%20%2F%20ES-blueviolet" alt="i18n EN/ES">
</p>

<h1 align="center">PairSync</h1>
<p align="center"><em>Code together, think together.</em></p>
<p align="center">Sesiones de <strong>pair programming</strong> en tiempo real, con roles de Driver/Navigator y un tutor de IA integrado en el chat.</p>

---

## Descripción

**PairSync** es una aplicación web construida sobre Laravel que recrea la técnica de *pair programming* de forma remota: dos personas comparten una misma sala mediante un código de 6 caracteres, se les asigna automáticamente un rol (**Driver** o **Navigator**) y pueden intercambiarlos en cualquier momento durante la sesión.

Este proyecto nace como **proyecto de grado**, explorando cómo la sincronización en tiempo real y un asistente conversacional (LangGraph) pueden apoyar el aprendizaje colaborativo de programación — en este caso, orientado a Android y Kotlin.

## Características

- **Salas por código** — se genera un código único de 6 caracteres al crear una sesión; el compañero se une con ese código.
- **Roles Driver / Navigator** — el creador de la sala es el Driver (escribe el código) y quien se une es el Navigator (guía la estrategia); los roles se pueden intercambiar (`swap`) durante la sesión.
- **Sincronización en tiempo real** — *polling* cada 2 segundos mantiene a ambos dispositivos al día sobre el estado de la sesión, los roles y el historial del chat.
- **Chat con tutor de IA** — un asistente conversacional (*Android Kotlin Tutor*) integrado vía [LangGraph](https://www.langchain.com/langgraph), con respuestas en streaming (SSE) y renderizado básico de Markdown/código.
- **Historial persistente** — el hilo de conversación y los mensajes del chat se guardan en base de datos, para que ambos participantes vean el mismo historial al recargar o reconectarse.
- **Internacionalización** — interfaz disponible en inglés y español (`lang/es.json`), con selector de idioma persistido en sesión.

## Cómo funciona

1. Un usuario crea una sala (`/session/create`) indicando su nombre → se genera un código y queda como **Driver**, en estado `waiting`.
2. Otro usuario se une con ese código (`/session/join`) → queda como **Navigator** y la sesión pasa a `active`.
3. Dentro de la sala (`/room/{code}`), ambos ven en tiempo real quién es el Driver/Navigator, pueden intercambiar roles y chatear con el tutor de IA.
4. Todo el estado (roles, hilo de LangGraph, historial de chat) se persiste en la tabla `pair_sessions`, por lo que la sesión sobrevive a recargas de página.

## Stack técnico

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Blade + JavaScript (vanilla), Tailwind CSS 4 |
| Build | Vite 7 |
| Base de datos | SQLite (por defecto, configurable a MySQL/PostgreSQL) |
| IA conversacional | LangGraph (agente externo vía API) |

## Requisitos

- PHP >= 8.2
- Composer
- Node.js + npm
- Una instancia de agente LangGraph (URL, API key y `agent_id`) si se quiere usar el chat con IA

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

Agrega las credenciales del agente de LangGraph a tu `.env`:

```env
LANGRAPH_API_URL=
LANGRAPH_API_KEY=
LANGRAPH_AGENT_ID=
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
app/Http/Controllers/SessionController.php   # Lógica de creación/unión/roles/chat
app/Models/PairSession.php                   # Modelo de la sesión de pareja
app/Http/Middleware/SetLocale.php            # Cambio de idioma por sesión
database/migrations/                         # Tablas pair_sessions (+ thread_id, chat_history)
resources/views/pair/                        # Vistas de landing y sala
lang/es.json                                 # Traducciones al español
```

## Autor

Proyecto de grado desarrollado por **Cristian Pérez** ([@cperez233](https://github.com/cperez233)).
