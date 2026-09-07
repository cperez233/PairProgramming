<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Register') }} — PairSync</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Sharing CSS foundation with login */
        :root {
            --bg:        #050511;
            --border:    #1a1b36;
            --border-hi: #2a2c56;
            --primary:   #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.4);
            --blue:      #3b82f6;
            --text:      #f8fafc;
            --text-dim:  #94a3b8;
            --error:     #ef4444;
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
            padding: 40px 0;
        }

        body::before {
            content: ''; position: fixed; inset: 0;
            background-image: linear-gradient(var(--border) 1px, transparent 1px), linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 50px 50px; opacity: .15; pointer-events: none; z-index: 0;
        }

        .ambient-glow {
            position: fixed; border-radius: 50%; filter: blur(120px); opacity: .12;
            pointer-events: none; z-index: 0; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 600px; height: 600px; background: var(--blue);
        }

        .auth-container { position: relative; z-index: 10; width: 100%; max-width: 480px; padding: 24px; }

        .logo { display: flex; align-items: center; justify-content: center; gap: 12px; text-decoration: none; margin-bottom: 32px; }
        .logo-icon { width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary), var(--blue)); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 0 20px var(--primary-glow); }
        .logo-name { font-weight: 800; font-size: 1.6rem; color: #fff; letter-spacing: -.02em; }
        .logo-name span { color: var(--primary); }

        .auth-card {
            background: rgba(10, 11, 22, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.06);
            border-radius: var(--radius); padding: 40px; box-shadow: 0 24px 64px rgba(0,0,0,0.4);
        }

        h1 { font-size: 1.6rem; font-weight: 700; text-align: center; margin-bottom: 8px; letter-spacing: -.03em; }
        .subtitle { text-align: center; color: var(--text-dim); font-size: 0.95rem; margin-bottom: 32px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-dim); margin-bottom: 8px; }

        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%; background: rgba(255,255,255,0.03); border: 1px solid var(--border-hi); border-radius: 10px;
            padding: 14px 16px; color: #fff; font-family: 'JetBrains Mono', monospace; font-size: 0.9rem; outline: none; transition: all 0.2s;
            appearance: none;
        }
        input:focus, select:focus { border-color: var(--primary); background: rgba(99, 102, 241, 0.05); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15); }
        
        select option {
            background-color: #0a0b16;
            color: #f8fafc;
        }

        .select-wrapper { position: relative; }
        .select-wrapper::after {
            content: "▼"; position: absolute; right: 16px; top: 50%; transform: translateY(-50%);
            color: var(--text-dim); pointer-events: none; font-size: 0.8rem;
        }

        .hidden { display: none !important; }

        .error-message { display: block; color: var(--error); font-size: 0.8rem; margin-top: 6px; }

        .btn-primary {
            width: 100%; padding: 14px; background: linear-gradient(135deg, var(--primary), var(--blue)); border-radius: 10px;
            color: #fff; font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1rem; border: none; cursor: pointer;
            transition: all 0.2s; box-shadow: 0 4px 15px var(--primary-glow); margin-top: 10px;
        }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6); }

        .auth-footer { text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-dim); }
        .auth-footer a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }
        
        @media (max-width: 480px) { .auth-card { padding: 32px 24px; } }
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
        <h1>{{ __('Create an Account') }}</h1>
        <p class="subtitle">{{ __('Join PairSync to start coding together') }}</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('Full Name') }}</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="role">{{ __('I am a...') }}</label>
                <div class="select-wrapper">
                    <select id="role" name="role" required onchange="toggleTeacherId()">
                        <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>{{ __('Student') }}</option>
                        <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>{{ __('Teacher') }}</option>
                    </select>
                </div>
                @error('role')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" id="teacher_id_group">
                <label for="teacher_id" id="teacher_id_label">{{ __('Teacher Code (Optional)') }}</label>
                <input id="teacher_id" type="text" name="teacher_id" value="{{ old('teacher_id') }}" placeholder="e.g. TCH-001">
                @error('teacher_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">{{ __('Email Address') }}</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn-primary">
                {{ __('Create Account') }}
            </button>
        </form>

        <div class="auth-footer">
            {{ __('Already have an account?') }} 
            <a href="{{ route('login') }}">{{ __('Log in') }}</a>
        </div>
    </div>
</div>

<script>
    function toggleTeacherId() {
        const role = document.getElementById('role').value;
        const teacherLabel = document.getElementById('teacher_id_label');
        const teacherInput = document.getElementById('teacher_id');
        
        if (role === 'teacher') {
            teacherLabel.textContent = "{{ __('Teacher Verification ID') }}";
            teacherInput.placeholder = "e.g. TCH-123";
        } else {
            teacherLabel.textContent = "{{ __('Teacher Code (Optional)') }}";
            teacherInput.placeholder = "e.g. TCH-001";
        }
    }

    // Run on init in case of validation errors with old()
    window.addEventListener('DOMContentLoaded', toggleTeacherId);
</script>

</body>
</html>
