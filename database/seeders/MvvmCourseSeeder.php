<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MvvmCourseSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a default teacher
        $teacher = User::where('role', 'teacher')->first();
        if (!$teacher) {
            $teacher = User::create([
                'name' => 'Demo Teacher',
                'email' => 'teacher@example.com',
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'teacher_id' => 'TCH-001'
            ]);
        }

        // Create MVVM in Android Course
        $course = Course::create([
            'user_id' => $teacher->id,
            'icon' => '📱',
            'title' => 'Android MVVM con Kotlin',
            'description' => 'Aprende a estructurar aplicaciones Android escalables y robustas usando la arquitectura MVVM (Model-View-ViewModel), LiveData y Coroutines.',
            'level' => 'intermediate',
            'color' => '#3ddc84', // Android Green
        ]);

        // Lessons Data (Notice moodle_assignment_id is currently null, it needs to be updated with real Instance IDs from Moodle)
        $lessons = [
            [
                'title'                => 'Introducción a Patrones de Arquitectura',
                'description'          => 'MVC vs MVP vs MVVM. Por qué Google recomienda MVVM para Android.',
                'duration'             => '25 min',
                'icon'                 => '🏗️',
                'available'            => true,
                'moodle_assignment_id' => 2,
                'content'              => $this->getLesson1Content(),
            ],
            [
                'title'                => 'La Capa de Datos: Modelos y Repositorios',
                'description'          => 'Separation of concerns: Cómo aislar el acceso a datos de la interfaz de usuario.',
                'duration'             => '30 min',
                'icon'                 => '💾',
                'available'            => true,
                'moodle_assignment_id' => 3,
            ],
            [
                'title'                => 'ViewModel y Gestión de Estado',
                'description'          => 'Ciclo de vida del ViewModel, StateFlow y LiveData para notificar a la vista.',
                'duration'             => '40 min',
                'icon'                 => '🧠',
                'available'            => true,
                'moodle_assignment_id' => 4,
            ],
            [
                'title'                => 'La Vista: Consumiendo el ViewModel',
                'description'          => 'Observando el estado desde Fragments o Jetpack Compose de forma segura.',
                'duration'             => '35 min',
                'icon'                 => '👁️',
                'available'            => true,
                'moodle_assignment_id' => 5,
            ],
            [
                'title'                => 'Inyección de Dependencias con Hilt',
                'description'          => 'Cómo inyectar repositorios en ViewModels para facilitar el testing.',
                'duration'             => '45 min',
                'icon'                 => '💉',
                'available'            => true,
                'moodle_assignment_id' => 6,
            ],
            [
                'title'                => 'Proyecto Final: App del Clima MVVM',
                'description'          => 'Aplicación completa aplicando MVVM, consumiendo una API REST real.',
                'duration'             => '60 min',
                'icon'                 => '🌤️',
                'available'            => true,
                'moodle_assignment_id' => 7,
            ],
        ];

        foreach ($lessons as $index => $lessonData) {
            $course->lessons()->create(array_merge($lessonData, ['order' => $index + 1]));
        }
    }

    private function getLesson1Content(): array
    {
        return [
            'sections' => [
                [
                    'type'  => 'intro',
                    'title' => 'Bienvenido a la Arquitectura Android',
                    'body'  => 'A medida que las aplicaciones crecen, poner todo el código dentro de un Activity o Fragment se vuelve insostenible (conocido como God Activity). La arquitectura de software nos ayuda a separar las responsabilidades. Hoy exploraremos por qué MVVM se convirtió en el estándar oficial de Android.',
                ],
                [
                    'type'  => 'concept',
                    'title' => 'MVC vs MVP vs MVVM',
                    'body'  => 'A lo largo de los años, Android ha evolucionado a través de varios patrones:',
                    'bullets' => [
                        '**MVC (Model-View-Controller)**: Difícil de testear en Android porque el Activity actúa como Vista y Controlador al mismo tiempo.',
                        '**MVP (Model-View-Presenter)**: Mejora el testing abstrayendo la lógica a un Presenter, pero requiere interfaces pesadas de comunicación.',
                        '**MVVM (Model-View-ViewModel)**: La Vista observa reactivamente al ViewModel. El ViewModel no sabe nada de la Vista, solucionando fugas de memoria y sobreviviendo a cambios de configuración (como rotar la pantalla).',
                    ],
                ],
                [
                    'type'  => 'code',
                    'title' => 'El problema sin Arquitectura',
                    'body'  => 'Imagina una Activity que hace peticiones de red directamente:',
                    'code'  => 'class BadActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)
        
        // ❌ MALA PRÁCTICA: Lógica de negocio y red mezclada con la UI
        lifecycleScope.launch {
            try {
                val users = apiService.getUsers()
                findViewById<TextView>(R.id.tvUsers).text = users.joinToString()
            } catch (e: Exception) {
                // Manejo de error mezclado en la vista
            }
        }
    }
}',
                    'language' => 'kotlin',
                    'note'  => 'Si el usuario rota la pantalla durante la petición, la Activity se destruye, la red se cancela (o causa memory leak) y se tiene que volver a pedir todo.',
                ],
                [
                    'type'  => 'concept',
                    'title' => 'La Solución: El ViewModel',
                    'body'  => 'Un ViewModel sobrevive a los cambios de configuración. Su único trabajo es procesar los datos del Repositorio y exponerlos como un flujo (StateFlow) o LiveData para que la Vista los consuma de manera pasiva.',
                ],
                [
                    'type'  => 'exercise',
                    'title' => '🎯 Pair Programming Exercise',
                    'body'  => 'Discutan con su compañero: Imaginen una aplicación de carrito de compras. ¿Qué código pertenecería al View, cuál al ViewModel y cuál al Model/Repository?',
                    'tasks' => [
                        [
                            'title' => 'Task 1: Clasificación de Responsabilidades',
                            'description' => 'Escribe comentarios indicando a qué capa de MVVM pertenece cada acción.',
                            'hint' => 'View = Mostrar UI y recibir clics. ViewModel = Lógica de presentación y mantener estado. Repository = Base de datos o Red.',
                            'starter_code' => '// TODO: Indica a qué capa (View, ViewModel, o Model) pertenece cada acción:

// 1. Mostrar un Toast de "Añadido al carrito": [ESCRIBE AQUÍ]
// 2. Hacer el INSERT en la base de datos SQL: [ESCRIBE AQUÍ]
// 3. Calcular el precio total con el impuesto: [ESCRIBE AQUÍ]
// 4. Capturar el clic del botón "Comprar": [ESCRIBE AQUÍ]
// 5. Transformar la lista de productos a una lista de nombres en String para el RecyclerView: [ESCRIBE AQUÍ]',
                        ],
                    ],
                ],
                [
                    'type'  => 'summary',
                    'title' => 'Resumen de MVVM',
                    'bullets' => [
                        '**Model (Data Layer)**: Repositorios, APIs, Bases de datos. La fuente única de la verdad.',
                        '**ViewModel**: Mantiene el estado de la UI y ejecuta la lógica de negocio llamando al Model.',
                        '**View**: Activities, Fragments o Compose. Solo se encarga de pintar la pantalla basada en el estado del ViewModel.',
                    ],
                ],
            ],
        ];
    }
}
