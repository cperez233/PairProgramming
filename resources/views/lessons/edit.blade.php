<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Edit Lesson Content') }} — PairSync</title>

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
        body { font-family: 'Syne', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; flex-direction: column; }
        
        nav { position: sticky; top: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; padding: 16px 32px; background: rgba(5, 5, 17, 0.95); border-bottom: 1px solid rgba(255,255,255,0.05); }
        .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-icon { width: 32px; height: 32px; background: linear-gradient(135deg, var(--primary), var(--blue)); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px; box-shadow: 0 0 16px var(--primary-glow); }
        .logo-name { font-weight: 800; font-size: 1.2rem; color: #fff; letter-spacing: -.02em; }
        .logo-name span { color: var(--primary); }
        .btn-nav-outline { display: inline-flex; align-items: center; padding: 8px 16px; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; text-decoration: none; font-weight: 600; font-size: 0.85rem; background: rgba(255,255,255,0.02); transition: all 0.2s; }
        .btn-nav-outline:hover { background: rgba(255,255,255,0.05); }
        .btn-primary { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: linear-gradient(135deg, var(--primary), var(--blue)); border-radius: 8px; color: #fff; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px var(--primary-glow); }

        .container { max-width: 900px; margin: 40px auto; padding: 0 24px 80px; width: 100%; }
        
        .header { margin-bottom: 30px; }
        .header-badge { display: inline-block; padding: 6px 12px; background: rgba(255,255,255,0.05); border-radius: 100px; font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; color: var(--text-dim); margin-bottom: 12px; }
        h1 { font-size: 2.2rem; font-weight: 800; }

        .builder-container { display: flex; flex-direction: column; gap: 24px; margin-bottom: 40px; }
        
        /* Section Card */
        .section-card { background: rgba(10, 11, 22, 0.85); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; overflow: hidden; position: relative; transition: border-color 0.2s; }
        .section-card:hover { border-color: rgba(255,255,255,0.15); }
        .section-header { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(255,255,255,0.05); }
        .section-type-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; font-family: 'JetBrains Mono', monospace; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 10px; border-radius: 6px; }
        
        .type-intro { background: rgba(99, 102, 241, 0.1); color: #818cf8; }
        .type-concept { background: rgba(250, 204, 21, 0.1); color: #facc15; }
        .type-code { background: rgba(52, 211, 153, 0.1); color: #34d399; }
        .type-summary { background: rgba(56, 189, 248, 0.1); color: #38bdf8; }
        .type-exercise { background: rgba(244, 114, 182, 0.1); color: #f472b6; }

        .btn-remove { background: none; border: none; color: var(--text-lo); cursor: pointer; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; transition: all 0.2s; }
        .btn-remove:hover { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .section-body { padding: 20px; display: flex; flex-direction: column; gap: 16px; }
        
        /* Form elements */
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        label { font-size: 0.85rem; font-weight: 600; color: var(--text-dim); }
        input[type="text"], textarea { width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff; font-family: 'Syne', sans-serif; font-size: 0.95rem; }
        input[type="text"]:focus, textarea:focus { outline: none; border-color: var(--primary); }
        textarea { resize: vertical; min-height: 80px; }
        textarea.code-editor { font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #34d399; min-height: 150px; background: rgba(0,0,0,0.5); }
        
        .array-container { display: flex; flex-direction: column; gap: 10px; }
        .array-item { display: flex; gap: 10px; }
        .array-item input { flex: 1; }
        .btn-array-add { align-self: flex-start; background: rgba(255,255,255,0.05); border: 1px dashed rgba(255,255,255,0.2); color: var(--text-dim); padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: 600; }
        .btn-array-add:hover { background: rgba(255,255,255,0.1); color: #fff; }

        /* Toolbar */
        .toolbar { display: flex; flex-wrap: wrap; gap: 12px; padding: 20px; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.15); border-radius: 12px; align-items: center; justify-content: center; }
        .toolbar-title { width: 100%; text-align: center; font-size: 0.85rem; color: var(--text-dim); font-weight: 600; margin-bottom: 4px; }
        .btn-tool { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; color: #fff; cursor: pointer; font-weight: 600; font-size: 0.85rem; transition: all 0.2s; }
        .btn-tool:hover { background: rgba(255,255,255,0.1); transform: translateY(-1px); }

    </style>
</head>
<body>

<nav>
    <a href="{{ url('/') }}" class="logo">
        <div class="logo-icon">⌨</div>
        <span class="logo-name">Pair<span>Sync</span></span>
    </a>
    <div style="display: flex; gap: 16px;">
        <a href="{{ route('courses.edit', $lesson->course_id) }}" class="btn-nav-outline">{{ __('Cancel') }}</a>
        <button type="button" class="btn-primary" onclick="saveContent()">💾 {{ __('Save Content') }}</button>
    </div>
</nav>

<div class="container">
    <div class="header">
        <div class="header-badge">{{ $lesson->course->title }} › Lesson {{ $lesson->id }}</div>
        <h1>{{ __('Edit Content') }}: {{ $lesson->title }}</h1>
    </div>

    <!-- Hidden form to submit data -->
    <form id="contentForm" action="{{ route('lessons.updateContent', $lesson->id) }}" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="content_json" id="content_json_input">
    </form>

    <div class="builder-container" id="builder">
        <!-- Sections will be rendered here -->
    </div>

    <div class="toolbar">
        <div class="toolbar-title">{{ __('Add a new section') }}</div>
        <button class="btn-tool" onclick="addSection('intro')">📖 Intro</button>
        <button class="btn-tool" onclick="addSection('concept')">💡 Concept</button>
        <button class="btn-tool" onclick="addSection('code')">💻 Code</button>
        <button class="btn-tool" onclick="addSection('summary')">✅ Summary</button>
        <button class="btn-tool" onclick="addSection('exercise')">🎯 Exercise</button>
    </div>
</div>

<script>
    // Initial data from DB
    let sectionsData = @json($lesson->content['sections'] ?? []);
    if (!Array.isArray(sectionsData)) sectionsData = [];

    const builder = document.getElementById('builder');

    // Section templates
    const templates = {
        intro: (id) => `
            <div class="form-group">
                <label>Title</label>
                <input type="text" class="field-title" placeholder="Welcome to...">
            </div>
            <div class="form-group">
                <label>Body Content (Markdown allowed)</label>
                <textarea class="field-body" placeholder="Write introduction here..."></textarea>
            </div>
        `,
        concept: (id) => `
            <div class="form-group">
                <label>Title</label>
                <input type="text" class="field-title" placeholder="Concept title">
            </div>
            <div class="form-group">
                <label>Body Content (Optional)</label>
                <textarea class="field-body" placeholder="Explain the concept..."></textarea>
            </div>
            <div class="form-group">
                <label>Bullet Points</label>
                <div class="array-container field-bullets" id="bullets-${id}"></div>
                <button type="button" class="btn-array-add" onclick="addArrayItem('bullets-${id}')">+ Add Bullet Point</button>
            </div>
        `,
        code: (id) => `
            <div class="form-group">
                <label>Title</label>
                <input type="text" class="field-title" placeholder="Example Code">
            </div>
            <div class="form-group">
                <label>Description (Optional)</label>
                <textarea class="field-body" placeholder="Describe what this code does..."></textarea>
            </div>
            <div class="form-group" style="display:flex; flex-direction:row; gap:16px;">
                <div style="flex:1;">
                    <label>Language</label>
                    <input type="text" class="field-language" placeholder="kotlin" value="kotlin">
                </div>
                <div style="flex:2;">
                    <label>Footer Note (Optional)</label>
                    <input type="text" class="field-note" placeholder="💡 Remember to...">
                </div>
            </div>
            <div class="form-group">
                <label>Code Snippet</label>
                <textarea class="field-code code-editor" placeholder="fun main() { ... }"></textarea>
            </div>
        `,
        summary: (id) => `
            <div class="form-group">
                <label>Title</label>
                <input type="text" class="field-title" value="What You Learned">
            </div>
            <div class="form-group">
                <label>Bullet Points</label>
                <div class="array-container field-bullets" id="bullets-${id}"></div>
                <button type="button" class="btn-array-add" onclick="addArrayItem('bullets-${id}')">+ Add Point</button>
            </div>
        `,
        exercise: (id) => `
            <div class="form-group">
                <label>Title</label>
                <input type="text" class="field-title" value="🎯 Pair Programming Exercise">
            </div>
            <div class="form-group">
                <label>Body Content</label>
                <textarea class="field-body" placeholder="Instructions for the exercise..."></textarea>
            </div>
            <div class="form-group">
                <label>Tasks</label>
                <div class="array-container field-tasks" id="tasks-${id}"></div>
                <button type="button" class="btn-array-add" onclick="addTaskItem('tasks-${id}')">+ Add Task</button>
            </div>
        `
    };

    function generateId() {
        return Math.random().toString(36).substr(2, 9);
    }

    // Render a section to the DOM
    function createSectionElement(type, data = {}) {
        const id = generateId();
        const el = document.createElement('div');
        el.className = 'section-card';
        el.dataset.type = type;
        el.dataset.id = id;

        // Header
        const header = document.createElement('div');
        header.className = 'section-header';
        
        let icon = '📄';
        if (type === 'intro') icon = '📖';
        if (type === 'concept') icon = '💡';
        if (type === 'code') icon = '💻';
        if (type === 'summary') icon = '✅';
        if (type === 'exercise') icon = '🎯';

        header.innerHTML = `
            <span class="section-type-badge type-${type}">${icon} ${type}</span>
            <button class="btn-remove" onclick="this.closest('.section-card').remove()">×</button>
        `;
        
        // Body
        const body = document.createElement('div');
        body.className = 'section-body';
        body.innerHTML = templates[type](id);

        el.appendChild(header);
        el.appendChild(body);

        // Populate existing data
        setTimeout(() => {
            if (data.title) el.querySelector('.field-title').value = data.title;
            if (data.body && el.querySelector('.field-body')) el.querySelector('.field-body').value = data.body;
            if (data.code && el.querySelector('.field-code')) el.querySelector('.field-code').value = data.code;
            if (data.language && el.querySelector('.field-language')) el.querySelector('.field-language').value = data.language;
            if (data.note && el.querySelector('.field-note')) el.querySelector('.field-note').value = data.note;

            if (data.bullets && Array.isArray(data.bullets) && el.querySelector('.field-bullets')) {
                data.bullets.forEach(b => addArrayItem(`bullets-${id}`, b));
            }
            if (data.tasks && Array.isArray(data.tasks) && el.querySelector('.field-tasks')) {
                data.tasks.forEach(t => addTaskItem(`tasks-${id}`, t));
            }
        }, 0);

        return el;
    }

    function addArrayItem(containerId, value = '') {
        const container = document.getElementById(containerId);
        if (!container) return;
        const item = document.createElement('div');
        item.className = 'array-item';
        item.innerHTML = `
            <input type="text" class="array-input" placeholder="..." value="${value.replace(/"/g, '&quot;')}">
            <button type="button" class="btn-remove" onclick="this.parentElement.remove()" style="background:rgba(255,255,255,0.05); color:#fff;">×</button>
        `;
        container.appendChild(item);
    }

    function addTaskItem(containerId, data = {}) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const item = document.createElement('div');
        item.className = 'array-item task-item';
        item.style.flexDirection = 'column';
        item.style.background = 'rgba(0,0,0,0.2)';
        item.style.padding = '16px';
        item.style.borderRadius = '8px';
        item.style.border = '1px solid rgba(255,255,255,0.05)';
        
        item.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <span style="font-size:0.8rem; font-weight:700; color:var(--text-dim);">TASK</span>
                <button type="button" class="btn-remove" onclick="this.parentElement.parentElement.remove()" style="height:24px;width:24px;font-size:1rem;">×</button>
            </div>
            <input type="text" class="task-title" placeholder="Task Title" value="${(data.title || '').replace(/"/g, '&quot;')}" style="margin-bottom:8px;">
            <textarea class="task-desc" placeholder="Task description..." style="min-height:50px; margin-bottom:8px;">${data.description || ''}</textarea>
            <input type="text" class="task-hint" placeholder="Hint (optional)" value="${(data.hint || '').replace(/"/g, '&quot;')}" style="margin-bottom:8px;">
            <textarea class="task-code code-editor" placeholder="Starter code (optional)..." style="min-height:80px;">${data.starter_code || ''}</textarea>
        `;
        container.appendChild(item);
    }

    function addSection(type) {
        const el = createSectionElement(type);
        builder.appendChild(el);
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }

    // Initialize builder with existing data
    document.addEventListener('DOMContentLoaded', () => {
        sectionsData.forEach(section => {
            builder.appendChild(createSectionElement(section.type, section));
        });
        
        // If empty, add an intro by default
        if (sectionsData.length === 0) {
            addSection('intro');
        }
    });

    // Save Content (Serialize DOM back to JSON)
    function saveContent() {
        const sections = [];
        const cards = builder.querySelectorAll('.section-card');
        
        cards.forEach(card => {
            const type = card.dataset.type;
            const titleEl = card.querySelector('.field-title');
            const bodyEl = card.querySelector('.field-body');
            
            const section = { type: type };
            if (titleEl) section.title = titleEl.value;
            if (bodyEl && bodyEl.value.trim() !== '') section.body = bodyEl.value;
            
            if (type === 'code') {
                const codeEl = card.querySelector('.field-code');
                const langEl = card.querySelector('.field-language');
                const noteEl = card.querySelector('.field-note');
                if (codeEl) section.code = codeEl.value;
                if (langEl) section.language = langEl.value;
                if (noteEl && noteEl.value.trim() !== '') section.note = noteEl.value;
            }
            
            if (type === 'concept' || type === 'summary') {
                const bullets = [];
                card.querySelectorAll('.array-input').forEach(input => {
                    if (input.value.trim() !== '') bullets.push(input.value);
                });
                section.bullets = bullets;
            }
            
            if (type === 'exercise') {
                const tasks = [];
                card.querySelectorAll('.task-item').forEach(item => {
                    const tTitle = item.querySelector('.task-title').value;
                    const tDesc = item.querySelector('.task-desc').value;
                    const tHint = item.querySelector('.task-hint').value;
                    const tCode = item.querySelector('.task-code').value;
                    
                    if (tTitle.trim() !== '') {
                        tasks.push({
                            title: tTitle,
                            description: tDesc,
                            hint: tHint || null,
                            starter_code: tCode || null
                        });
                    }
                });
                section.tasks = tasks;
            }
            
            sections.push(section);
        });

        const finalJson = JSON.stringify({ sections: sections });
        document.getElementById('content_json_input').value = finalJson;
        document.getElementById('contentForm').submit();
    }
</script>

</body>
</html>
