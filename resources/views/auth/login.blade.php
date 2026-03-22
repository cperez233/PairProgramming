<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Log In') }} — PairSync</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg:        #050511;
            --surface:   #0a0b16;
            --border:    #1a1b36;
            --border-hi: #2a2c56;
            --primary:   #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.4);
            --blue:      #3b82f6;
            --text:      #f8fafc;
            --text-dim:  #94a3b8;
            --text-lo:   #475569;
            --error:     #ef4444;
            --error-bg:  rgba(239, 68, 68, 0.1);
            --radius:    16px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

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

        .ambient-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: .12;
            pointer-events: none;
            z-index: 0;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 600px; height: 600px;
            background: var(--primary);
        }

        .auth-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 24px;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            margin-bottom: 32px;
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
            font-size: 1.6rem;
            color: #fff;
            letter-spacing: -.02em;
        }
        .logo-name span { color: var(--primary); }

        .auth-card {
            background: rgba(10, 11, 22, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: var(--radius);
            padding: 40px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.4);
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -.03em;
        }

        .subtitle {
            text-align: center;
            color: var(--text-dim);
            font-size: 0.95rem;
            margin-bottom: 32px;
        }

        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dim);
            margin-bottom: 8px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border-hi);
            border-radius: 10px;
            padding: 14px 16px;
            color: #fff;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            transition: all 0.2s;
            outline: none;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .error-message {
            display: block;
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 6px;
        }

        .options-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 0.85rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-dim);
            cursor: pointer;
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--blue));
            border-radius: 10px;
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 15px var(--primary-glow);
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.9rem;
            color: var(--text-dim);
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        .auth-footer a:hover { text-decoration: underline; }
        
        @media (max-width: 480px) {
            .auth-card { padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="ambient-glow"></div>

<div class="auth-container">
    <a href="{{ url('/') }}" class="logo">
        <div class="logo-icon">⌨</div>
        <span class="logo-name">Pair<span>Sync</span></span>
    </a>

    <div class="auth-card">
        <h1>{{ __('Welcome Back') }}</h1>
        <p class="subtitle">{{ __('Log in to your account') }}</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">{{ __('Email Address') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="options-row">
                <label class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <span>{{ __('Remember me') }}</span>
                </label>
            </div>

            <button type="submit" class="btn-primary">
                {{ __('Log In') }}
            </button>
        </form>

        <div class="auth-footer">
            {{ __('Don\'t have an account?') }} 
            <a href="{{ route('register') }}">{{ __('Sign up') }}</a>
        </div>
    </div>
</div>

</body>
</html>
