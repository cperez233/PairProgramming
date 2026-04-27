<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room {{ $session->code }} — PairSync</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;500;700&family=Syne:wght@400;600;800&display=swap" rel="stylesheet">
    @include('pair.partials.room-styles')
</head>
<body>

{{-- ── TOPBAR ── --}}
<header class="topbar">
    <a href="{{ route('pair.index') }}" class="topbar-logo">Pair<span>Sync</span></a>
    <div class="topbar-divider"></div>
    <div class="session-badge">
        <span class="session-label">{{ __('Room') }}</span>
        <span class="session-code">{{ $session->code }}</span>
        <button class="copy-btn" onclick="copyCode()" title="{{ __('Copy code') }}">⧉</button>
    </div>
    <div class="topbar-divider"></div>
    <div id="role-indicator" class="role-indicator {{ $myRole === 'driver' ? 'is-driver' : ($myRole === 'navigator' ? 'is-navigator' : '') }}">
        @if($myRole === 'driver') 🧑‍💻 Driver @elseif($myRole === 'navigator') 🧭 Navigator @else 👀 ... @endif
    </div>
    <div class="topbar-spacer"></div>
    <div style="display:flex;gap:8px;align-items:center;margin-right:8px">
        <a href="{{ route('lang.switch', 'en') }}" style="color:{{ app()->getLocale()==='en'?'var(--green)':'var(--text-dim)' }};text-decoration:none;font-weight:bold;font-size:.75rem">EN</a>
        <span style="color:var(--border)">/</span>
        <a href="{{ route('lang.switch', 'es') }}" style="color:{{ app()->getLocale()==='es'?'var(--green)':'var(--text-dim)' }};text-decoration:none;font-weight:bold;font-size:.75rem">ES</a>
    </div>
    <span class="status-pill {{ $session->status === 'waiting' ? 'pill-waiting' : 'pill-active' }}" id="status-pill">
        <span class="pill-dot"></span>
        <span id="status-text">{{ $session->status === 'waiting' ? __('Waiting for partner') : __('Session active') }}</span>
    </span>
</header>

{{-- ── IDE LAYOUT ── --}}
<div class="ide-container">

    {{-- ── EDITOR AREA ── --}}
    <div class="editor-area">
        <div class="editor-toolbar">
            <span class="lang-badge">Kotlin</span>
            <button class="btn-run" id="btn-run" onclick="runCode()">▶ Run</button>
            <span class="save-status" id="save-status">
                @if($myRole === 'driver') ✓ Ready @else 👁 Read-only @endif
            </span>
        </div>

        <div class="editor-wrapper">
            @if($myRole !== 'driver')
                <div class="readonly-badge" id="readonly-badge">👁 {{ __('Read-only · Navigator') }}</div>
            @else
                <div class="readonly-badge" id="readonly-badge" style="display:none">👁 {{ __('Read-only · Navigator') }}</div>
            @endif
            <div id="monaco-container"></div>
        </div>

        <div class="output-panel">
            <div class="output-header">
                <h4>▸ {{ __('Output') }}</h4>
                <button class="clear-btn" onclick="clearOutput()">{{ __('Clear') }}</button>
            </div>
            <div id="output-content"><span class="output-info">{{ __('Ready to run code...') }}</span></div>
        </div>
    </div>

    {{-- ── SIDEBAR ── --}}
    <aside class="sidebar">
        <div class="sidebar-section">
            <h3>👥 {{ __('Participants') }}</h3>
            <div class="participant p-driver {{ $myRole === 'driver' ? 'is-me' : '' }}" id="p-driver">
                <div class="participant-avatar">🧑‍💻</div>
                <div class="participant-info">
                    <div class="participant-role">{{ __('Driver') }}</div>
                    <div class="participant-name {{ $session->driver ? '' : 'empty' }}">
                        {{ $session->driver ?? __('waiting...') }}
                        @if($myRole === 'driver')<span class="you-tag">{{ __('You') }}</span>@endif
                    </div>
                </div>
            </div>
            <div class="participant p-navigator {{ $myRole === 'navigator' ? 'is-me' : '' }}" id="p-navigator">
                <div class="participant-avatar">🧭</div>
                <div class="participant-info">
                    <div class="participant-role">{{ __('Navigator') }}</div>
                    <div class="participant-name {{ $session->navigator ? '' : 'empty' }}">
                        {{ $session->navigator ?? __('waiting...') }}
                        @if($myRole === 'navigator')<span class="you-tag">{{ __('You') }}</span>@endif
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar-section" style="font-size:.72rem;color:var(--text-dim);line-height:1.6">
            <h3>⚡ {{ __('Tips') }}</h3>
            <p>▸ {{ __('Driver writes code, Navigator guides.') }}</p>
            <p>▸ {{ __('Swap roles every 15–25 min.') }}</p>
            <p>▸ {{ __('Use the AI tutor for help.') }}</p>
        </div>

        <div class="sidebar-actions">
            <button class="btn btn-swap" id="btn-swap" onclick="swapRoles(this)" style="{{ $session->status !== 'active' ? 'display:none' : '' }}">🔄 {{ __('Swap Roles') }}</button>
            <a href="{{ route('challenges.index') }}" class="btn btn-leave">← {{ __('Leave') }}</a>
        </div>
    </aside>
</div>

{{-- ── CHAT FAB ── --}}
<button id="chat-fab" onclick="toggleChat()" title="{{ __('Ask the Android Kotlin Tutor') }}">
    🤖
    <span class="badge" id="chat-badge"></span>
</button>

{{-- ── CHAT PANEL ── --}}
<div id="chat-panel">
    <div class="chat-header">
        <div class="chat-header-icon">🤖</div>
        <div style="flex:1">
            <div class="chat-header-name">{{ __('Android Kotlin Tutor') }}</div>
            <div class="chat-header-status">{{ __('online · shared session') }}</div>
        </div>
        <button class="chat-close" onclick="toggleChat()">✕</button>
    </div>
    <div id="chat-messages">
        <div class="chat-empty" id="chat-empty">
            <div class="chat-empty-icon">🤖</div>
            <h4>{{ __('Android Kotlin Tutor') }}</h4>
            <p>{{ __('Ask me anything about Android or Kotlin. Both of you can see this conversation.') }}</p>
        </div>
    </div>
    <div class="chat-input-area">
        <textarea id="chat-input" placeholder="{{ __('Ask about Kotlin, Android, Jetpack...') }}" rows="1" onkeydown="handleKey(event)" oninput="autoResize(this)"></textarea>
        <button id="chat-send" onclick="sendMessage()" title="{{ __('Send') }}">➤</button>
    </div>
</div>

<div id="toast">{{ __('Copied!') }}</div>

{{-- ── MONACO LOADER ── --}}
<script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs/loader.js"></script>

@include('pair.partials.room-scripts')

</body>
</html>