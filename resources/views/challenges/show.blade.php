<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __($challenge['title']) }} — PairSync</title>
    <meta name="description" content="{{ __($challenge['description']) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg:           #050511;
            --surface:      #0a0b16;
            --card-bg:      rgba(10, 11, 22, 0.85);
            --border:       #1a1b36;
            --border-hi:    #2a2c56;
            --primary:      #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.4);
            --blue:         #3b82f6;
            --green:        #10b981;
            --text:         #f8fafc;
            --text-dim:     #94a3b8;
            --text-lo:      #475569;
            --radius:       16px;
            --accent:       {{ $challenge['color'] }};
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ── GRID BACKGROUND ── */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            height: 100%;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: .15;
            pointer-events: none;
            z-index: 0;
            will-change: transform;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 600px 600px at 80% 5%, {{ $challenge['color'] }}18, transparent),
                radial-gradient(ellipse 400px 400px at 10% 80%, rgba(59, 130, 246, 0.06), transparent);
            pointer-events: none;
            z-index: 0;
        }

        /* ── NAVIGATION ── */
        nav {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 48px;
            background: rgba(5, 5, 17, 0.92);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--blue));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 0 20px var(--primary-glow);
        }

        .logo-name {
            font-weight: 800;
            font-size: 1.4rem;
            color: #fff;
            letter-spacing: -.02em;
        }

        .logo-name span { color: var(--primary); }

        .nav-divider {
            width: 1px;
            height: 20px;
            background: rgba(255,255,255,0.1);
        }

        .nav-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
        }

        .nav-breadcrumb a {
            color: var(--text-dim);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .nav-breadcrumb a:hover { color: #fff; }

        .nav-breadcrumb .separator { color: var(--text-lo); }

        .nav-breadcrumb .current {
            color: var(--accent);
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .btn-nav-outline {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            background: rgba(255,255,255,0.02);
        }

        .btn-nav-outline:hover {
            border-color: rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.05);
            transform: translateY(-1px);
        }

        .nav-user {
            color: var(--text-dim);
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* ── HERO ── */
        .hero {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 0 auto;
            padding: 56px 24px 32px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: {{ $challenge['color'] }}15;
            border: 1px solid {{ $challenge['color'] }}30;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: clamp(2rem, 4.5vw, 3rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -.03em;
            margin-bottom: 14px;
        }

        .hero h1 .accent { color: var(--accent); }

        .hero p {
            font-size: clamp(0.95rem, 1.5vw, 1.1rem);
            color: var(--text-dim);
            line-height: 1.6;
            max-width: 600px;
            font-weight: 500;
        }

        .hero-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .hero-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .hero-meta-item .label { color: var(--text-dim); }
        .hero-meta-item .value { color: #fff; }

        .level-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 100px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .level-beginner {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .level-intermediate {
            background: rgba(59, 130, 246, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .level-advanced {
            background: rgba(250, 204, 21, 0.12);
            color: #facc15;
            border: 1px solid rgba(250, 204, 21, 0.2);
        }

        /* ── LESSONS LIST ── */
        .lessons-section {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 0 auto;
            padding: 0 24px 80px;
        }

        .lessons-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .lessons-header h2 {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: -.02em;
        }

        .lessons-count-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: var(--text-dim);
        }

        .lesson-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* ── LESSON CARD ── */
        .lesson-card {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 22px 24px;
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: var(--radius);
            text-decoration: none;
            color: inherit;
            transition: transform 0.3s cubic-bezier(0.22, 1, 0.36, 1),
                        border-color 0.3s,
                        box-shadow 0.3s;
            will-change: transform;
            position: relative;
            overflow: hidden;
        }

        .lesson-card.available:hover {
            transform: translateY(-3px);
            border-color: {{ $challenge['color'] }}40;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
        }

        .lesson-card.locked {
            opacity: 0.5;
            cursor: default;
        }

        .lesson-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--accent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .lesson-card.available:hover::before {
            opacity: 1;
        }

        .lesson-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-lo);
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: border-color 0.3s, color 0.3s;
        }

        .lesson-card.available:hover .lesson-number {
            border-color: {{ $challenge['color'] }}40;
            color: var(--accent);
        }

        .lesson-icon {
            font-size: 1.6rem;
            flex-shrink: 0;
            width: 44px;
            text-align: center;
        }

        .lesson-info {
            flex: 1;
            min-width: 0;
        }

        .lesson-title {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
            letter-spacing: -.01em;
        }

        .lesson-description {
            font-size: 0.82rem;
            color: var(--text-dim);
            line-height: 1.5;
            font-weight: 500;
        }

        .lesson-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .lesson-duration {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: var(--text-lo);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .lesson-status {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.68rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 100px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .status-available {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-coming {
            background: rgba(255,255,255,0.04);
            color: var(--text-lo);
            border: 1px solid rgba(255,255,255,0.06);
        }

        .lesson-arrow {
            font-size: 1.1rem;
            color: var(--text-lo);
            transition: color 0.2s, transform 0.2s;
        }

        .lesson-card.available:hover .lesson-arrow {
            color: var(--accent);
            transform: translateX(4px);
        }

        .lesson-lock {
            font-size: 1rem;
            opacity: 0.4;
        }

        /* ── FOOTER ── */
        footer {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 32px;
            color: var(--text-lo);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            border-top: 1px solid rgba(255,255,255,0.05);
            margin-top: auto;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 700px) {
            nav { padding: 16px 20px; flex-wrap: wrap; gap: 12px; }
            .nav-breadcrumb { font-size: 0.78rem; }
            .lesson-card { flex-wrap: wrap; gap: 12px; padding: 18px 16px; }
            .lesson-right { width: 100%; justify-content: flex-end; }
            .hero-meta { gap: 12px; }
        }

        /* ── ENTRANCE ANIMATIONS ── */
        .lesson-card {
            opacity: 0;
            transform: translateY(20px);
        }

        .lesson-card.visible {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.4s ease, transform 0.4s ease,
                        border-color 0.3s, box-shadow 0.3s;
        }

        .lesson-card.locked.visible {
            opacity: 0.5;
        }
    </style>
</head>
<body>

{{-- ── NAVIGATION ── --}}
<nav>
    <div class="nav-left">
        <a href="{{ url('/') }}" class="logo">
            <div class="logo-icon">⌨</div>
            <span class="logo-name">Pair<span>Sync</span></span>
        </a>
        <div class="nav-divider"></div>
        <div class="nav-breadcrumb">
            <a href="{{ route('challenges.index') }}">{{ __('Challenges') }}</a>
            <span class="separator">›</span>
            <span class="current">{{ __($challenge['title']) }}</span>
        </div>
    </div>

    <div class="nav-links">
        @auth
            <span class="nav-user">{{ auth()->user()->name }}</span>
            <a href="{{ route('challenges.index') }}" class="btn-nav-outline">← {{ __('Back') }}</a>
        @endauth
    </div>
</nav>

{{-- ── HERO ── --}}
<section class="hero">
    <div class="hero-badge">{{ $challenge['icon'] }} {{ __($challenge['title']) }}</div>
    <h1>
        {{ __($challenge['title']) }}
    </h1>
    <p>{{ __($challenge['description']) }}</p>

    <div class="hero-meta">
        <span class="level-badge level-{{ $challenge['level'] }}">{{ __($challenge['level']) }}</span>
        <div class="hero-meta-item">
            <span class="label">📚</span>
            <span class="value">{{ count($lessons) }} {{ __('lessons') }}</span>
        </div>
        <div class="hero-meta-item">
            <span class="label">⏱️</span>
            <span class="value">~{{ collect($lessons)->pluck('duration')->map(fn($d) => (int) $d)->sum() }} min {{ __('total') }}</span>
        </div>
    </div>
</section>

{{-- ── LESSONS LIST ── --}}
<section class="lessons-section">
    <div class="lessons-header">
        <h2>{{ __('Course Lessons') }}</h2>
        <span class="lessons-count-label">{{ $lessons->where('available', true)->count() }}/{{ $lessons->count() }} {{ __('available') }}</span>
    </div>

    <div class="lesson-list" id="lessonList">
        @foreach ($lessons as $lesson)
            @if ($lesson['available'])
                <a href="{{ route('challenges.lesson', [$challenge['id'], $lesson['id']]) }}"
                   class="lesson-card available"
                   data-index="{{ $loop->index }}"
                   id="lesson-card-{{ $lesson['id'] }}">
            @else
                <div class="lesson-card locked" data-index="{{ $loop->index }}">
            @endif

                <div class="lesson-number">#{{ str_pad($lesson['id'], 2, '0', STR_PAD_LEFT) }}</div>
                <div class="lesson-icon">{{ $lesson['icon'] }}</div>
                <div class="lesson-info">
                    <div class="lesson-title">{{ __($lesson['title']) }}</div>
                    <div class="lesson-description">{{ __($lesson['description']) }}</div>
                </div>
                <div class="lesson-right">
                    <span class="lesson-duration">🕐 {{ $lesson['duration'] }}</span>
                    @if ($lesson['available'])
                        <span class="lesson-status status-available">{{ __('Available') }}</span>
                        <span class="lesson-arrow">→</span>
                    @else
                        <span class="lesson-status status-coming">{{ __('Coming Soon') }}</span>
                        <span class="lesson-lock">🔒</span>
                    @endif
                </div>

            @if ($lesson['available'])
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>
</section>

<footer>
    {{ __('PairSync · Designed for developers, built for teams.') }}
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cards = document.querySelectorAll('.lesson-card');
        cards.forEach((card, i) => {
            setTimeout(() => {
                card.classList.add('visible');
            }, 100 + i * 60);
        });
    });
</script>

</body>
</html>
