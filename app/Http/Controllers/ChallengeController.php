<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index()
    {
        $challenges = $this->getChallenges();
        return view('challenges.index', compact('challenges'));
    }

    public function show(int $id)
    {
        $challenges = $this->getChallenges();
        $challenge = collect($challenges)->firstWhere('id', $id);

        if (!$challenge) {
            abort(404);
        }

        $lessons = $this->getLessons($id);

        return view('challenges.show', compact('challenge', 'lessons'));
    }

    public function lesson(int $challengeId, int $lessonId)
    {
        $challenges = $this->getChallenges();
        $challenge = collect($challenges)->firstWhere('id', $challengeId);

        if (!$challenge) {
            abort(404);
        }

        $lessons = $this->getLessons($challengeId);
        $lesson = collect($lessons)->firstWhere('id', $lessonId);

        if (!$lesson) {
            abort(404);
        }

        return view('challenges.lesson', compact('challenge', 'lesson', 'lessons'));
    }

    // ── Data helpers ──────────────────────────────────────────────────

    private function getChallenges(): array
    {
        return [
            [
                'id'          => 1,
                'icon'        => '🟣',
                'title'       => 'Kotlin Fundamentals',
                'description' => 'Learn the core syntax, data types, control flow, and object-oriented features of Kotlin — the modern language for Android.',
                'level'       => 'beginner',
                'lessons'     => 12,
                'color'       => '#a78bfa',
            ],
            [
                'id'          => 2,
                'icon'        => '🎨',
                'title'       => 'UI with Jetpack Compose',
                'description' => 'Build modern, declarative user interfaces with Jetpack Compose. Layouts, theming, state management and animations.',
                'level'       => 'intermediate',
                'lessons'     => 15,
                'color'       => '#38bdf8',
            ],
            [
                'id'          => 3,
                'icon'        => '🏗️',
                'title'       => 'MVVM Architecture',
                'description' => 'Master the Model-View-ViewModel pattern. Structure your Android projects with ViewModels, LiveData, and clean separation of concerns.',
                'level'       => 'intermediate',
                'lessons'     => 10,
                'color'       => '#34d399',
            ],
            [
                'id'          => 4,
                'icon'        => '🌐',
                'title'       => 'REST APIs with Retrofit',
                'description' => 'Consume web services in your Android app. HTTP clients, JSON parsing with Moshi/Gson, coroutines, and error handling.',
                'level'       => 'intermediate',
                'lessons'     => 8,
                'color'       => '#fb923c',
            ],
            [
                'id'          => 5,
                'icon'        => '💾',
                'title'       => 'Local Storage with Room',
                'description' => 'Persist data locally with Room database. Entities, DAOs, migrations, and integration with Kotlin coroutines and Flow.',
                'level'       => 'intermediate',
                'lessons'     => 9,
                'color'       => '#f472b6',
            ],
            [
                'id'          => 6,
                'icon'        => '🚀',
                'title'       => 'Final Project',
                'description' => 'Put it all together: build a complete Android application integrating Compose, MVVM, Retrofit, and Room from scratch.',
                'level'       => 'advanced',
                'lessons'     => 6,
                'color'       => '#facc15',
            ],
        ];
    }

    private function getLessons(int $challengeId): array
    {
        $lessonsMap = [
            1 => [
                [
                    'id'          => 1,
                    'title'       => 'Introduction to Kotlin',
                    'description' => 'What is Kotlin, why it matters for Android, and writing your first program.',
                    'duration'    => '25 min',
                    'icon'        => '👋',
                    'available'   => true,
                    'content'     => $this->getLesson1Content(),
                ],
                [
                    'id'          => 2,
                    'title'       => 'Variables & Data Types',
                    'description' => 'val vs var, type inference, and primitive data types in Kotlin.',
                    'duration'    => '20 min',
                    'icon'        => '📦',
                    'available'   => false,
                    'content'     => null,
                ],
                [
                    'id'          => 3,
                    'title'       => 'Operators & Expressions',
                    'description' => 'Arithmetic, comparison, logical operators and operator overloading.',
                    'duration'    => '20 min',
                    'icon'        => '➕',
                    'available'   => false,
                    'content'     => null,
                ],
                [
                    'id'          => 4,
                    'title'       => 'Control Flow: if, when',
                    'description' => 'Conditional expressions, when blocks, and branching logic.',
                    'duration'    => '25 min',
                    'icon'        => '🔀',
                    'available'   => false,
                    'content'     => null,
                ],
                [
                    'id'          => 5,
                    'title'       => 'Loops & Ranges',
                    'description' => 'for, while, do-while loops and Kotlin range expressions.',
                    'duration'    => '20 min',
                    'icon'        => '🔁',
                    'available'   => false,
                    'content'     => null,
                ],
                [
                    'id'          => 6,
                    'title'       => 'Functions & Lambdas',
                    'description' => 'Defining functions, default parameters, single-expression functions, and lambda syntax.',
                    'duration'    => '30 min',
                    'icon'        => '⚡',
                    'available'   => false,
                    'content'     => null,
                ],
                [
                    'id'          => 7,
                    'title'       => 'Null Safety',
                    'description' => 'Nullable types, safe calls, the Elvis operator, and non-null assertions.',
                    'duration'    => '25 min',
                    'icon'        => '🛡️',
                    'available'   => false,
                    'content'     => null,
                ],
                [
                    'id'          => 8,
                    'title'       => 'Collections: Lists, Sets, Maps',
                    'description' => 'Immutable and mutable collections, iteration, and common operations.',
                    'duration'    => '30 min',
                    'icon'        => '📚',
                    'available'   => false,
                    'content'     => null,
                ],
                [
                    'id'          => 9,
                    'title'       => 'Classes & Objects',
                    'description' => 'Constructors, properties, methods, data classes, and companion objects.',
                    'duration'    => '35 min',
                    'icon'        => '🏛️',
                    'available'   => false,
                    'content'     => null,
                ],
                [
                    'id'          => 10,
                    'title'       => 'Inheritance & Interfaces',
                    'description' => 'Open classes, abstract classes, interfaces, and polymorphism.',
                    'duration'    => '30 min',
                    'icon'        => '🧬',
                    'available'   => false,
                    'content'     => null,
                ],
                [
                    'id'          => 11,
                    'title'       => 'Extension Functions & Scope Functions',
                    'description' => 'let, run, with, apply, also — and writing your own extensions.',
                    'duration'    => '25 min',
                    'icon'        => '🔧',
                    'available'   => false,
                    'content'     => null,
                ],
                [
                    'id'          => 12,
                    'title'       => 'Kotlin Fundamentals Review',
                    'description' => 'Comprehensive review, mini-project, and preparation for the next challenge.',
                    'duration'    => '40 min',
                    'icon'        => '🎯',
                    'available'   => false,
                    'content'     => null,
                ],
            ],
        ];

        return $lessonsMap[$challengeId] ?? [];
    }

    // ── Lesson content ────────────────────────────────────────────────

    private function getLesson1Content(): array
    {
        return [
            'sections' => [
                [
                    'type'  => 'intro',
                    'title' => 'Welcome to Kotlin!',
                    'body'  => 'Kotlin is a modern, concise, and safe programming language developed by JetBrains. In 2019, Google announced Kotlin as the preferred language for Android development. Today, over 95% of the top 1,000 Android apps use Kotlin. In this lesson, you and your partner will learn the absolute fundamentals — enough to start writing real programs together.',
                ],
                [
                    'type'  => 'concept',
                    'title' => 'Why Kotlin?',
                    'body'  => 'Kotlin was designed to be better than Java in every practical way while remaining fully interoperable with it. Here are the key advantages:',
                    'bullets' => [
                        '**Concise** — Drastically reduces boilerplate code. A Java class of 50 lines might be 5 in Kotlin.',
                        '**Null-safe** — The type system distinguishes nullable and non-nullable types, eliminating NullPointerException at compile time.',
                        '**Interoperable** — Kotlin compiles to JVM bytecode, so you can call Java from Kotlin and vice versa.',
                        '**Coroutines** — First-class support for asynchronous programming, making network calls and UI updates clean and simple.',
                        '**Official Android language** — Google provides Kotlin-first APIs, documentation, and tooling.',
                    ],
                ],
                [
                    'type'  => 'code',
                    'title' => 'Your First Kotlin Program',
                    'body'  => 'Every Kotlin program starts with a `main` function. Unlike Java, you don\'t need a class to wrap it — Kotlin supports top-level functions.',
                    'code'  => 'fun main() {
    println("Hello, Kotlin!")
    println("Welcome to PairSync 🚀")
}',
                    'language' => 'kotlin',
                    'note'  => '`fun` declares a function. `println()` prints a line to the console. No semicolons needed!',
                ],
                [
                    'type'  => 'concept',
                    'title' => 'Variables: val vs var',
                    'body'  => 'Kotlin has two keywords for declaring variables. This distinction helps you write safer, more predictable code:',
                    'bullets' => [
                        '`val` — **Immutable** (read-only). Once assigned, it cannot be reassigned. Prefer `val` whenever possible.',
                        '`var` — **Mutable**. Can be reassigned to a new value at any time.',
                    ],
                ],
                [
                    'type'  => 'code',
                    'title' => 'Variables in Action',
                    'body'  => 'Notice how Kotlin uses **type inference** — you don\'t need to specify the type if the compiler can figure it out:',
                    'code'  => 'fun main() {
    // Immutable — cannot be reassigned
    val language = "Kotlin"       // Type inferred as String
    val year: Int = 2016          // Explicit type annotation
    val isModern = true           // Inferred as Boolean

    // Mutable — can be reassigned
    var score = 0
    score = 10                    // ✅ This is allowed
    score = score + 5             // score is now 15

    // val language = "Java"      // ❌ Error! val cannot be reassigned

    println("$language was released in $year")
    println("Your score: $score")
}',
                    'language' => 'kotlin',
                    'note'  => 'The `$variable` syntax inside strings is called **string templates** — one of Kotlin\'s most loved features.',
                ],
                [
                    'type'  => 'concept',
                    'title' => 'Basic Data Types',
                    'body'  => 'Kotlin has the following basic data types. Unlike Java, there are no primitive types — everything is an object:',
                    'bullets' => [
                        '`Int` — Whole numbers: `42`, `-7`, `1_000_000`',
                        '`Double` — Decimal numbers: `3.14`, `-0.5`',
                        '`Float` — Lower precision decimals: `3.14f`',
                        '`Boolean` — `true` or `false`',
                        '`String` — Text: `"Hello"`, `"Kotlin"`',
                        '`Char` — Single character: `\'A\'`, `\'z\'`',
                        '`Long` — Large whole numbers: `9_999_999_999L`',
                    ],
                ],
                [
                    'type'  => 'code',
                    'title' => 'Functions in Kotlin',
                    'body'  => 'Functions are declared with the `fun` keyword. Kotlin makes functions concise and expressive:',
                    'code'  => '// Standard function with return type
fun greet(name: String): String {
    return "Hello, $name! Welcome to Android development."
}

// Single-expression function (shorter syntax)
fun add(a: Int, b: Int): Int = a + b

// Function with default parameter
fun createUser(name: String, role: String = "student"): String {
    return "$name ($role)"
}

fun main() {
    println(greet("Driver"))
    println(greet("Navigator"))

    println("Sum: ${add(3, 7)}")

    println(createUser("Alex"))                // Alex (student)
    println(createUser("Sam", "instructor"))    // Sam (instructor)
}',
                    'language' => 'kotlin',
                    'note'  => 'Default parameters eliminate the need for method overloading — a common pattern in Java.',
                ],
                [
                    'type'  => 'concept',
                    'title' => 'String Templates',
                    'body'  => 'String templates are one of Kotlin\'s most practical features. You can embed variables and expressions directly inside strings:',
                    'bullets' => [
                        '`$variable` — Inserts the value of a variable',
                        '`${expression}` — Evaluates an expression and inserts the result',
                        'Triple-quoted strings `"""..."""` for multiline text',
                    ],
                ],
                [
                    'type'  => 'code',
                    'title' => 'String Templates in Practice',
                    'body'  => '',
                    'code'  => 'fun main() {
    val name = "Kotlin"
    val version = 2.0

    // Simple variable insertion
    println("Learning $name")

    // Expression insertion
    println("Next version: ${version + 0.1}")

    // Multiline string
    val menu = """
        |=== PairSync ===
        |1. Create Room
        |2. Join Room
        |3. View Challenges
    """.trimMargin()

    println(menu)
}',
                    'language' => 'kotlin',
                    'note'  => '`trimMargin()` removes the leading `|` and whitespace, keeping your multiline strings clean.',
                ],
                [
                    'type'  => 'exercise',
                    'title' => '🎯 Pair Programming Exercise',
                    'body'  => 'Now it\'s time to practice! Create a pair programming session and work on this exercise together. The **Driver** types the code while the **Navigator** guides and reviews.',
                    'tasks' => [
                        [
                            'title' => 'Task 1: Student Profile',
                            'description' => 'Create a program that stores a student\'s information using `val` and `var`, then prints a formatted profile card using string templates.',
                            'hint' => 'Use val for name, university, and major (they don\'t change). Use var for semester and GPA (they can change).',
                            'starter_code' => 'fun main() {
    // TODO: Declare student information
    // val name = ...
    // val university = ...
    // var semester = ...
    // var gpa = ...

    // TODO: Print a formatted profile card
    // Use string templates to create output like:
    // ╔═══════════════════════════════╗
    // ║  Student Profile              ║
    // ║  Name: Alex                   ║
    // ║  University: MIT              ║
    // ║  Semester: 4                  ║
    // ║  GPA: 3.8                    ║
    // ╚═══════════════════════════════╝

    // TODO: Update semester and GPA, then print again
}',
                        ],
                        [
                            'title' => 'Task 2: Simple Calculator',
                            'description' => 'Write functions for basic math operations (add, subtract, multiply, divide) that take two Double parameters and return a Double. Then call them from main.',
                            'hint' => 'Try using single-expression functions: fun add(a: Double, b: Double): Double = a + b',
                            'starter_code' => 'fun add(a: Double, b: Double): Double = a + b

// TODO: Write subtract, multiply, divide functions

fun main() {
    val x = 10.0
    val y = 3.0

    println("$x + $y = ${add(x, y)}")
    // TODO: Print results for subtract, multiply, divide
    // Be careful with divide by zero!
}',
                        ],
                    ],
                ],
                [
                    'type'  => 'summary',
                    'title' => 'What You Learned',
                    'bullets' => [
                        'Kotlin is the preferred language for Android development',
                        '`fun main()` is the entry point of every Kotlin program',
                        '`val` for immutable variables, `var` for mutable ones',
                        'Kotlin has type inference — you often don\'t need to write types explicitly',
                        'Functions use the `fun` keyword and support default parameters',
                        'String templates (`$var` and `${expr}`) make string formatting elegant',
                    ],
                ],
            ],
        ];
    }
}
