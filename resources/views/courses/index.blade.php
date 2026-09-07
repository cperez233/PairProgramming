<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Manage Courses') }} — PairSync</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg:           #050511;
            --surface:      #0a0b16;
            --border:       #1a1b36;
            --primary:      #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.4);
            --blue:         #3b82f6;
            --text:         #f8fafc;
            --text-dim:     #94a3b8;
            --text-lo:      #475569;
            --radius:       16px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

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
        }

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

        .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-icon {
            width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary), var(--blue));
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-size: 18px; box-shadow: 0 0 20px var(--primary-glow);
        }
        .logo-name { font-weight: 800; font-size: 1.4rem; color: #fff; letter-spacing: -.02em; }
        .logo-name span { color: var(--primary); }
        .nav-links { display: flex; align-items: center; gap: 24px; }
        .btn-nav-outline {
            display: inline-flex; align-items: center; padding: 10px 20px;
            border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;
            color: #fff; text-decoration: none; font-weight: 600; font-size: 0.9rem;
            transition: all 0.2s; background: rgba(255,255,255,0.02);
        }
        .btn-nav-outline:hover {
            border-color: rgba(255,255,255,0.2); background: rgba(255,255,255,0.05);
            transform: translateY(-1px);
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 1000px;
            margin: 60px auto;
            padding: 0 24px;
            width: 100%;
        }

        .header-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 800;
        }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px;
            background: linear-gradient(135deg, var(--primary), var(--blue));
            border-radius: 10px; color: #fff; text-decoration: none;
            font-weight: 700; transition: transform 0.2s, box-shadow 0.2s;
            border: none; cursor: pointer;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--primary-glow);
        }

        .table-container {
            background: rgba(10, 11, 22, 0.85);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: var(--radius);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 16px 24px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        th {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        td {
            font-weight: 500;
        }

        tr:last-child td { border-bottom: none; }
        
        .empty-state {
            padding: 60px 24px;
            text-align: center;
            color: var(--text-dim);
        }

        .actions {
            display: flex;
            gap: 12px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.1);
            color: var(--text-dim);
            transition: all 0.2s;
            cursor: pointer;
            background: transparent;
        }

        .btn-sm:hover {
            color: #fff;
            border-color: rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.05);
        }
        
        .btn-danger:hover {
            color: #ef4444;
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }
        
        .alert {
            padding: 16px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #34d399;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<nav>
    <a href="{{ url('/') }}" class="logo">
        <div class="logo-icon">⌨</div>
        <span class="logo-name">Pair<span>Sync</span></span>
    </a>
    <div class="nav-links">
        <a href="{{ route('challenges.index') }}" class="btn-nav-outline">← {{ __('Back to Challenges') }}</a>
    </div>
</nav>

<div class="container">
    @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    <div class="header-action">
        <h1>{{ __('Manage Courses') }}</h1>
        <a href="{{ route('courses.create') }}" class="btn-primary">
            <span>+</span> {{ __('Create Course') }}
        </a>
    </div>

    <div class="table-container">
        @if($courses->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Course') }}</th>
                        <th>{{ __('Level') }}</th>
                        <th>{{ __('Lessons') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 1.5rem;">{{ $course->icon }}</span>
                                    <div>
                                        <div style="font-weight: 700; color: #fff;">{{ $course->title }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; padding: 4px 10px; border-radius: 100px; background: rgba(255,255,255,0.05);">
                                    {{ ucfirst($course->level) }}
                                </span>
                            </td>
                            <td>{{ $course->lessons_count }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('courses.edit', $course->id) }}" class="btn-sm">{{ __('Edit') }}</a>
                                    <form action="{{ route('courses.destroy', $course->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this course?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-sm btn-danger">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <p>{{ __('You haven\'t created any courses yet.') }}</p>
            </div>
        @endif
    </div>
</div>

</body>
</html>
