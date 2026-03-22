<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PairSync — Collaborative Coding</title>

    <meta name="description" content="Real-time collaborative pair programming with PairSync. Code together with synchronized editors and roles.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;700&family=Syne:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:        #050511;
            --surface:   #0a0b16;
            --border:    #1a1b36;
            --border-hi: #2a2c56;
            --primary:   #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.5);
            --green:     #10b981;
            --blue:      #3b82f6;
            --text:      #f8fafc;
            --text-dim:  #94a3b8;
            --text-lo:   #475569;
            --radius:    16px;
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
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: .15;
            pointer-events: none;
            z-index: 0;
        }

        /* ── AMBIENT GLOW ── */
        .ambient-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(140px);
            opacity: .15;
            pointer-events: none;
            z-index: 0;
            animation: breathe 8s infinite alternate ease-in-out;
        }
        .glow-1 { width: 800px; height: 800px; background: var(--primary); top: -200px; left: -200px; }
        .glow-2 { width: 600px; height: 600px; background: var(--blue); bottom: -100px; right: -100px; }
        .glow-3 { width: 500px; height: 500px; background: var(--green); top: 30%; left: 50%; transform: translate(-50%, -50%); opacity: 0.1; }

        @keyframes breathe {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.1) translate(20px, 20px); }
        }

        /* ── NAVIGATION ── */
        nav {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 48px;
            background: rgba(5, 5, 17, 0.7);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
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

        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .nav-link {
            color: var(--text-dim);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: color 0.2s;
        }

        .nav-link:hover { color: #fff; }

        .btn-outline {
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

        .btn-outline:hover {
            border-color: rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.05);
            transform: translateY(-1px);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            padding: 10px 24px;
            background: linear-gradient(135deg, var(--primary), var(--blue));
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.2s;
            border: none;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
        }

        .lang-switch {
            display: flex;
            gap: 8px;
            border-right: 1px solid var(--border);
            padding-right: 24px;
        }

        /* ── HERO SECTION ── */
        .hero {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 80px 24px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 100px;
            color: var(--primary);
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 32px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .hero h1 {
            font-size: clamp(3rem, 7vw, 5.5rem);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -.03em;
            margin-bottom: 24px;
            text-wrap: balance;
        }

        .hero h1 .gradient-text {
            background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
        }

        .hero h1 .accent-text {
            background: linear-gradient(135deg, var(--primary), var(--blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: clamp(1.1rem, 2vw, 1.4rem);
            color: var(--text-dim);
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto 48px;
            font-weight: 500;
        }

        .cta-group {
            display: flex;
            gap: 16px;
            align-items: center;
            justify-content: center;
        }

        .btn-large {
            padding: 16px 40px;
            font-size: 1.1rem;
            border-radius: 12px;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            padding: 16px 32px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.2);
        }

        /* ── FEATURE SHOWCASE ── */
        .features {
            position: relative;
            z-index: 1;
            padding: 60px 24px 100px;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .feature-card {
            background: rgba(10, 11, 22, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: var(--radius);
            padding: 32px;
            transition: transform 0.3s, border-color 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #fff;
        }

        .feature-card p {
            color: var(--text-dim);
            font-size: 0.95rem;
            line-height: 1.6;
            font-weight: 500;
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

        @media (max-width: 900px) {
            .features { grid-template-columns: 1fr; }
            nav { padding: 20px; }
        }

        @media (max-width: 600px) {
            .cta-group { flex-direction: column; width: 100%; }
            .cta-group > * { width: 100%; text-align: center; justify-content: center; }
            .lang-switch { border-right: none; padding-right: 0; }
            .nav-links { gap: 12px; }
            .btn-outline { display: none; }
        }
    </style>
</head>
<body>

<div class="ambient-glow glow-1"></div>
<div class="ambient-glow glow-2"></div>
<div class="ambient-glow glow-3"></div>

<nav>
    <a href="{{ url('/') }}" class="logo">
        <div class="logo-icon">⌨</div>
        <span class="logo-name">Pair<span>Sync</span></span>
    </a>
    
    <div class="nav-links">
        <div class="lang-switch">
            <a href="{{ route('lang.switch', 'en') }}" style="color: {{ app()->getLocale() === 'en' ? 'var(--primary)' : 'var(--text-dim)' }}; text-decoration: none; font-weight: 700;">EN</a>
            <span style="color: var(--text-lo);">/</span>
            <a href="{{ route('lang.switch', 'es') }}" style="color: {{ app()->getLocale() === 'es' ? 'var(--primary)' : 'var(--text-dim)' }}; text-decoration: none; font-weight: 700;">ES</a>
        </div>
        
        @auth
            <a href="{{ route('pair.index') }}" class="btn-outline">{{ __('Dashboard') }}</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;margin:0;">
                @csrf
                <button type="submit" class="nav-link" style="background:none;border:none;cursor:pointer;">{{ __('Log out') }}</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="nav-link">{{ __('Log in') }}</a>
            <a href="{{ route('register') }}" class="btn-primary">{{ __('Get Started') }}</a>
        @endauth
    </div>
</nav>

<main class="hero">
    <div class="hero-badge">✨ {{ __('Next-Gen Collaboration') }}</div>
    
    <h1>
        <span class="gradient-text">{{ __('Code together,') }}</span>
        <span class="accent-text">{{ __('think together.') }}</span>
    </h1>
    
    <p>{{ __('Real-time pair programming sessions. One driver, one navigator — both perfectly in sync with intelligent assistance.') }}</p>
    
    <div class="cta-group">
        @auth
            <a href="{{ route('pair.index') }}" class="btn-primary btn-large">{{ __('Open App') }} &rarr;</a>
        @else
            <a href="{{ route('register') }}" class="btn-primary btn-large">{{ __('Get Started') }} &rarr;</a>
            <a href="{{ route('login') }}" class="btn-secondary">{{ __('Log in') }}</a>
        @endauth
    </div>
</main>

<div class="features">
    <div class="feature-card">
        <div class="feature-icon" style="color: var(--green);">🧑‍💻</div>
        <h3>{{ __('The Driver') }}</h3>
        <p>{{ __('Controls the keyboard. Focuses on the tactical implementation of the immediate task at hand.') }}</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon" style="color: var(--blue);">🧭</div>
        <h3>{{ __('The Navigator') }}</h3>
        <p>{{ __('Reviews code in real time, thinks about direction, architecture, and catches potential bugs early.') }}</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon" style="color: var(--primary);">🤖</div>
        <h3>{{ __('AI Assistant') }}</h3>
        <p>{{ __('Built-in intelligent tutor to help unblock you, explain concepts, and provide suggestions as you code.') }}</p>
    </div>
</div>

<footer>
    {{ __('PairSync · Designed for developers, built for teams.') }}
</footer>

</body>
</html>
