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

        // Wipe old course if it exists to avoid duplicates during testing
        Course::where('title', 'Android MVVM con Kotlin')->delete();

        // Create MVVM in Android Course
        $course = Course::create([
            'user_id' => $teacher->id,
            'icon' => '📱',
            'title' => 'Android MVVM con Kotlin',
            'description' => 'Aprende a estructurar aplicaciones Android escalables y robustas usando la arquitectura MVVM (Model-View-ViewModel), LiveData y Coroutines.',
            'level' => 'intermediate',
            'color' => '#3ddc84', // Android Green
        ]);

        // Lessons Data
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
                'content'              => $this->getLesson2Content(),
            ],
            [
                'title'                => 'ViewModel y Gestión de Estado',
                'description'          => 'Ciclo de vida del ViewModel, StateFlow y LiveData para notificar a la vista.',
                'duration'             => '40 min',
                'icon'                 => '🧠',
                'available'            => true,
                'moodle_assignment_id' => 4,
                'content'              => $this->getLesson3Content(),
            ],
            [
                'title'                => 'La Vista: Consumiendo el ViewModel',
                'description'          => 'Observando el estado desde Fragments o Jetpack Compose de forma segura.',
                'duration'             => '35 min',
                'icon'                 => '👁️',
                'available'            => true,
                'moodle_assignment_id' => 5,
                'content'              => $this->getLesson4Content(),
            ],
            [
                'title'                => 'Inyección de Dependencias con Hilt',
                'description'          => 'Cómo inyectar repositorios en ViewModels para facilitar el testing.',
                'duration'             => '45 min',
                'icon'                 => '💉',
                'available'            => true,
                'moodle_assignment_id' => 6,
                'content'              => $this->getLesson5Content(),
            ],
            [
                'title'                => 'Proyecto Final: App del Clima MVVM',
                'description'          => 'Aplicación completa aplicando MVVM, consumiendo una API REST real.',
                'duration'             => '60 min',
                'icon'                 => '🌤️',
                'available'            => true,
                'moodle_assignment_id' => 7,
                'content'              => $this->getLesson6Content(),
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
                    'title' => 'Fundamentos de la Arquitectura Android',
                    'body'  => 'En los inicios de Android, era común escribir toda la lógica de negocio, llamadas a red y manipulación de vistas dentro del `Activity` o `Fragment`. Esto generaba archivos gigantes conocidos como "God Activities". Para resolver esto, Google impulsó la **Arquitectura Recomendada**, basada fuertemente en el patrón **MVVM**.',
                ],
                [
                    'type'  => 'concept',
                    'title' => 'Evolución: De MVC a MVVM',
                    'body'  => 'Entender la historia te ayudará a valorar MVVM:',
                    'bullets' => [
                        '**MVC (Model-View-Controller)**: En Android, el Activity actúa como Controlador y Vista al mismo tiempo. Es imposible hacer test unitarios sin usar un emulador.',
                        '**MVP (Model-View-Presenter)**: Separa la lógica en un `Presenter`. El problema es que requiere crear decenas de Interfaces (`Contract`) para comunicar el Presenter con el Activity.',
                        '**MVVM (Model-View-ViewModel)**: Utiliza **programación reactiva**. El `ViewModel` expone "Estados" (usando StateFlow o LiveData). La Vista simplemente se suscribe a esos estados. ¡El ViewModel no sabe que la vista existe!',
                    ],
                ],
                [
                    'type'  => 'code',
                    'title' => 'Anatomía de un God Activity (Lo que NO debes hacer)',
                    'body'  => 'Mira este ejemplo. Si rotas el dispositivo mientras se hace la petición web, la Activity se destruye, la petición se pierde, y cuando termine, intentará actualizar un TextView que ya no existe (causando un crash).',
                    'code'  => 'class BadActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)
        
        val textView = findViewById<TextView>(R.id.tvData)
        
        // ❌ Mezcla de UI y Lógica de Negocio
        lifecycleScope.launch {
            try {
                textView.text = "Cargando..."
                // Llamada directa a base de datos o red desde la vista
                val data = api.getData() 
                textView.text = data.name
            } catch (e: Exception) {
                textView.text = "Error de red"
            }
        }
    }
}',
                    'language' => 'kotlin',
                    'note'  => 'El acoplamiento es tan fuerte que no puedes probar la lógica de `api.getData()` sin arrancar la UI.',
                ],
                [
                    'type'  => 'concept',
                    'title' => 'Flujo Unidireccional de Datos (UDF)',
                    'body'  => 'MVVM promueve el **Unidirectional Data Flow**. Los Eventos fluyen hacia arriba (View -> ViewModel), y el Estado fluye hacia abajo (ViewModel -> View).',
                    'bullets' => [
                        '**1. Evento**: El usuario presiona "Refrescar". La Vista llama a `viewModel.refresh()`.',
                        '**2. Proceso**: El ViewModel pide datos al Repositorio.',
                        '**3. Estado**: El ViewModel actualiza su `StateFlow` a `Loading` y luego a `Success`.',
                        '**4. Render**: La Vista, al estar observando el `StateFlow`, se repinta automáticamente.',
                    ],
                ],
                [
                    'type'  => 'exercise',
                    'title' => '🎯 Pair Programming: Diseñando la separación',
                    'body'  => 'Antes de escribir código MVVM, es vital saber clasificar las responsabilidades. Tienen 5 minutos para resolver este ejercicio juntos.',
                    'tasks' => [
                        [
                            'title' => 'Clasificación de Responsabilidades',
                            'description' => 'Agreguen comentarios indicando a qué capa (View, ViewModel, o Repository) pertenece cada responsabilidad.',
                            'hint' => 'View = UI y clics. ViewModel = Transformar datos para la UI y estado. Repository = Fetching de datos crudos.',
                            'starter_code' => '// TODO: Indica a qué capa (View, ViewModel, o Repository) pertenece cada tarea:

// 1. Mostrar un Snackbar de "Error de conexión": [ESCRIBE AQUÍ]
// 2. Hacer el query SELECT * FROM users en la base de datos SQL: [ESCRIBE AQUÍ]
// 3. Transformar "2023-10-01" en "1 de Octubre" para mostrarlo al usuario: [ESCRIBE AQUÍ]
// 4. Capturar el texto de un EditText: [ESCRIBE AQUÍ]
// 5. Decidir si se debe mostrar una animación de carga o no: [ESCRIBE AQUÍ]',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getLesson2Content(): array
    {
        return [
            'sections' => [
                [
                    'type'  => 'intro',
                    'title' => 'La Capa de Datos (Data Layer)',
                    'body'  => 'La capa de datos contiene la lógica de negocio de la aplicación. Está compuesta por **Modelos** (clases de datos) y **Repositorios**. El ViewModel **jamás** debe hacer peticiones a Retrofit o Room directamente; siempre debe pedirle los datos al Repositorio.',
                ],
                [
                    'type'  => 'concept',
                    'title' => 'El Patrón Repositorio',
                    'body'  => 'Un Repositorio es una clase que centraliza el acceso a datos. Actúa como mediador entre diferentes fuentes de datos (ej. Base de datos local vs API remota).',
                    'bullets' => [
                        '**Single Source of Truth**: El repositorio decide de dónde sacar los datos (¿Los saco de la caché local o hago una petición a internet?).',
                        '**Abstracción**: Al ViewModel no le importa de dónde vienen los datos, solo los consume.',
                        '**Mapeo**: Transforma los Data Transfer Objects (DTOs) de la red en Modelos de Dominio que la app entiende.',
                    ],
                ],
                [
                    'type'  => 'code',
                    'title' => 'Implementando un Repositorio en Kotlin',
                    'body'  => 'Así se ve un Repositorio clásico en Kotlin usando Coroutines:',
                    'code'  => 'class UserRepository(
    private val api: UserApi,
    private val dao: UserDao
) {
    // Retornamos un Flow (Flujo reactivo de datos)
    fun getUser(userId: String): Flow<User> = flow {
        // 1. Emitir datos cacheados inmediatamente
        val cachedUser = dao.getUser(userId)
        if (cachedUser != null) {
            emit(cachedUser)
        }
        
        // 2. Traer de la red y actualizar caché
        try {
            val networkUser = api.fetchUser(userId)
            dao.saveUser(networkUser)
            emit(networkUser) // Emitir la versión fresca
        } catch (e: Exception) {
            // Manejar error (ej. sin internet)
        }
    }
}',
                    'language' => 'kotlin',
                    'note'  => 'Al usar Flow, la Vista verá primero los datos cacheados y un segundo después verá mágicamente los datos frescos sin hacer nada extra.',
                ],
                [
                    'type'  => 'exercise',
                    'title' => '🎯 Pair Programming: Tu primer Repositorio',
                    'body'  => 'Vamos a crear un repositorio para una app de Notas.',
                    'tasks' => [
                        [
                            'title' => 'Creando el NoteRepository',
                            'description' => 'Completa la función `getNotes` para que primero emita las notas locales y luego intente traer las remotas.',
                            'hint' => 'Usa el bloque flow { ... } y la palabra clave `emit()`.',
                            'starter_code' => 'class NoteRepository(val localDb: LocalDb, val remoteApi: RemoteApi) {
    
    // TODO: Implementa esta función usando "flow"
    fun getNotes(): Flow<List<Note>> {
        // 1. Obtén las notas de localDb.getAllNotes()
        // 2. Emítelas
        // 3. Obtén las notas de remoteApi.fetchNotes()
        // 4. Guárdalas en localDb con localDb.saveAll(notas)
        // 5. Emite las notas remotas
    }
}',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getLesson3Content(): array
    {
        return [
            'sections' => [
                [
                    'type'  => 'intro',
                    'title' => 'El Corazón de MVVM: El ViewModel',
                    'body'  => 'El `ViewModel` está diseñado para almacenar y administrar datos relacionados con la UI respetando el ciclo de vida. Si el usuario rota el teléfono, la Activity se destruye y se recrea, ¡pero el ViewModel sobrevive manteniendo intactos sus datos!',
                ],
                [
                    'type'  => 'concept',
                    'title' => 'StateFlow vs LiveData',
                    'body'  => 'Históricamente se usaba `LiveData` para exponer estado. Hoy, la recomendación de Google es usar **StateFlow** (parte de Kotlin Coroutines).',
                    'bullets' => [
                        '**LiveData**: Ligado a Android. Solo se actualiza en el Main Thread. Perfecto para Java.',
                        '**StateFlow**: Puro Kotlin. Funciona asíncronamente. Requiere un valor inicial. Perfecto para Clean Architecture.',
                    ],
                ],
                [
                    'type'  => 'code',
                    'title' => 'Estructurando un ViewModel moderno',
                    'body'  => 'Observa cómo encapsulamos el estado. Exponemos una versión inmutable (`StateFlow`) a la vista, y mantenemos una mutable (`MutableStateFlow`) privada.',
                    'code'  => 'class UsersViewModel(
    private val repository: UserRepository
) : ViewModel() {

    // Privado: El ViewModel puede modificarlo
    private val _uiState = MutableStateFlow<UiState>(UiState.Loading)
    
    // Público: La Vista SOLO puede observarlo
    val uiState: StateFlow<UiState> = _uiState.asStateFlow()

    init {
        loadUsers()
    }

    fun loadUsers() {
        viewModelScope.launch {
            _uiState.value = UiState.Loading
            try {
                val users = repository.getUsers()
                _uiState.value = UiState.Success(users)
            } catch (e: Exception) {
                _uiState.value = UiState.Error(e.message)
            }
        }
    }
}

// Sealed class para representar los 3 estados posibles
sealed class UiState {
    object Loading : UiState()
    data class Success(val data: List<User>) : UiState()
    data class Error(val message: String?) : UiState()
}',
                    'language' => 'kotlin',
                    'note'  => 'Las `sealed classes` son perfectas para modelar el estado de la UI porque obligan a manejar todos los casos posibles.',
                ],
                [
                    'type'  => 'exercise',
                    'title' => '🎯 Pair Programming: Contador con ViewModel',
                    'body'  => 'Vamos a crear el clásico ViewModel de un contador, asegurándonos de encapsular correctamente el estado.',
                    'tasks' => [
                        [
                            'title' => 'ViewModel de Contador',
                            'description' => 'Crea un MutableStateFlow privado y un StateFlow público. Implementa la función increment().',
                            'hint' => 'Recuerda inicializar el MutableStateFlow con 0.',
                            'starter_code' => 'class CounterViewModel : ViewModel() {
    
    // TODO: 1. Crea _count como MutableStateFlow(0)
    // TODO: 2. Crea count como StateFlow exponiendo _count.asStateFlow()
    
    fun increment() {
        // TODO: 3. Aumenta el valor de _count en 1
    }
}',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getLesson4Content(): array
    {
        return [
            'sections' => [
                [
                    'type'  => 'intro',
                    'title' => 'La Vista pasiva',
                    'body'  => 'En MVVM, la Vista debe ser tan tonta como sea posible. No debe tener ifs complejos ni calcular datos. Su único trabajo es recolectar (collect) el estado del ViewModel y pintar los widgets correspondientes.',
                ],
                [
                    'type'  => 'code',
                    'title' => 'Consumiendo StateFlow en Android (XML/ViewBinding)',
                    'body'  => 'Para observar un StateFlow de forma segura respecto al ciclo de vida (que no consuma batería si la app está en segundo plano), usamos `repeatOnLifecycle`.',
                    'code'  => 'class UsersActivity : AppCompatActivity() {
    
    private val viewModel: UsersViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        // Empezamos a recolectar el estado
        lifecycleScope.launch {
            // Solo recolecta cuando la app está visible (STARTED)
            repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.uiState.collect { state ->
                    render(state)
                }
            }
        }
    }
    
    private fun render(state: UiState) {
        when(state) {
            is UiState.Loading -> showProgressBar()
            is UiState.Success -> showUsersList(state.data)
            is UiState.Error -> showErrorSnackbar(state.message)
        }
    }
}',
                    'language' => 'kotlin',
                    'note'  => 'El bloque `when` con sealed classes es exhaustivo. Si añades un nuevo estado en el futuro, el compilador te obligará a manejarlo aquí.',
                ],
                [
                    'type'  => 'code',
                    'title' => 'Alternativa moderna: Jetpack Compose',
                    'body'  => 'En Jetpack Compose (el UI toolkit moderno de Android), consumir MVVM es ridículamente fácil y no requiere lifecycleScopes:',
                    'code'  => '@Composable
fun UsersScreen(viewModel: UsersViewModel) {
    // CollectAsState hace toda la magia por ti
    val state by viewModel.uiState.collectAsState()
    
    when (state) {
        is UiState.Loading -> CircularProgressIndicator()
        is UiState.Success -> UserList(users = (state as UiState.Success).data)
        is UiState.Error -> Text(text = "Error")
    }
}',
                    'language' => 'kotlin',
                ],
                [
                    'type'  => 'exercise',
                    'title' => '🎯 Pair Programming: Renderizando el Contador',
                    'body'  => 'Imagina que estás en un Fragment de Compose. Consume el contador que hiciste en la lección anterior.',
                    'tasks' => [
                        [
                            'title' => 'UI de Contador',
                            'description' => 'Recibe el estado y crea un botón que llame a increment() al pulsarlo.',
                            'hint' => 'Usa collectAsState(). Para el botón usa Button(onClick = { viewModel.increment() }).',
                            'starter_code' => '@Composable
fun CounterScreen(viewModel: CounterViewModel) {
    // TODO: 1. Observa el estado del count
    // val currentCount = ...
    
    Column {
        // TODO: 2. Muestra el número en un Text()
        
        // TODO: 3. Crea un Button que llame a viewModel.increment()
    }
}',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getLesson5Content(): array
    {
        return [
            'sections' => [
                [
                    'type'  => 'intro',
                    'title' => 'Inyección de Dependencias con Hilt',
                    'body'  => 'Hasta ahora hemos asumido que el ViewModel recibe el Repositorio mágicamente. En una app real, inicializar repositorios y sus dependencias (Retrofit, Room) a mano es un caos. Para esto usamos Inyección de Dependencias (DI).',
                ],
                [
                    'type'  => 'concept',
                    'title' => '¿Qué es Dagger Hilt?',
                    'body'  => 'Hilt es la librería oficial de DI para Android, construida sobre Dagger. Se encarga de instanciar clases y pasárselas a otras automáticamente.',
                    'bullets' => [
                        '**@HiltAndroidApp**: Se pone en la clase Application. Activa Hilt en toda la app.',
                        '**@AndroidEntryPoint**: Se pone en Activities/Fragments para indicar que Hilt inyectará cosas ahí.',
                        '**@Inject**: Le dice a Hilt "tú encárgate de construir esta clase por mí".',
                        '**@HiltViewModel**: Indica que este ViewModel debe ser inyectado.',
                    ],
                ],
                [
                    'type'  => 'code',
                    'title' => 'Hilt en Acción',
                    'body'  => 'Mira cómo Hilt nos salva de instanciar manualmente el repositorio:',
                    'code'  => '// 1. Le decimos a Hilt cómo construir el ViewModel
@HiltViewModel
class UsersViewModel @Inject constructor(
    private val repository: UserRepository
) : ViewModel() {
    // ...
}

// 2. Le decimos a Hilt cómo construir el Repositorio
class UserRepository @Inject constructor(
    private val api: UserApi
) {
    // ...
}

// 3. En nuestra Activity, simplemente lo pedimos:
@AndroidEntryPoint
class UsersActivity : AppCompatActivity() {
    // Hilt se encarga de crear el UserApi, pasárselo al UserRepository, 
    // y pasarle ese repositorio al UsersViewModel automáticamente. ¡Magia!
    private val viewModel: UsersViewModel by viewModels()
}',
                    'language' => 'kotlin',
                    'note'  => 'Si en el futuro `UserRepository` necesita una nueva dependencia (como una base de datos local), solo la añades al constructor y Hilt se encarga del resto. No tienes que modificar la Activity.',
                ],
                [
                    'type'  => 'exercise',
                    'title' => '🎯 Pair Programming: Inyectando',
                    'body'  => 'Anota las clases correctamente para que Hilt las inyecte.',
                    'tasks' => [
                        [
                            'title' => 'Anotaciones Hilt',
                            'description' => 'Agrega las anotaciones correspondientes (@HiltViewModel, @Inject constructor, @AndroidEntryPoint).',
                            'hint' => 'Revisa el código de arriba.',
                            'starter_code' => '// TODO: Anota esta clase para que sea un ViewModel inyectable
class WeatherViewModel (
    private val repository: WeatherRepository
) : ViewModel() { ... }

// TODO: Anota la Activity para que Hilt pueda inyectar el viewModel
class WeatherActivity : AppCompatActivity() {
    private val viewModel: WeatherViewModel by viewModels()
}',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getLesson6Content(): array
    {
        return [
            'sections' => [
                [
                    'type'  => 'intro',
                    'title' => 'Proyecto Final: App del Clima',
                    'body'  => '¡Felicidades por llegar hasta aquí! Vamos a juntar todo (Retrofit, Repository, ViewModel, StateFlow y Compose) en un pequeño proyecto real.',
                ],
                [
                    'type'  => 'concept',
                    'title' => 'Requerimientos',
                    'body'  => 'Deben construir una pantalla que permita buscar el clima de una ciudad.',
                    'bullets' => [
                        '**1. Model**: Crear un data class `Weather(val temp: Double, val condition: String)`.',
                        '**2. Api**: Interfaz Retrofit con `suspend fun getWeather(city: String): Weather`.',
                        '**3. Repository**: Clase que consuma la API y retorne un Flow.',
                        '**4. ViewModel**: Maneja el estado (Loading, Success, Error) y tiene una función `searchCity(name)`.',
                        '**5. UI**: Un TextField para escribir la ciudad y un Botón para buscar.',
                    ],
                ],
                [
                    'type'  => 'exercise',
                    'title' => '🎯 Pair Programming: Manos a la obra',
                    'body'  => 'Este es su proyecto de graduación. Escriban el esqueleto de la arquitectura.',
                    'tasks' => [
                        [
                            'title' => 'Capa de Datos y ViewModel',
                            'description' => 'Implementen el repositorio y el ViewModel usando inyección de dependencias falsa (o @Inject si prefieren).',
                            'hint' => 'Dividan el trabajo: Uno dicta la estructura del ViewModel y el otro escribe el Repositorio.',
                            'starter_code' => '// 1. State
sealed class WeatherState {
    object Idle : WeatherState()
    object Loading : WeatherState()
    data class Success(val temp: String) : WeatherState()
    data class Error(val msg: String) : WeatherState()
}

// 2. Repository
class WeatherRepository {
    // Simula una petición de red
    suspend fun fetchWeather(city: String): String {
        delay(1000)
        return "25°C Soleado en $city"
    }
}

// 3. TODO: Implementa el ViewModel
class WeatherViewModel(val repo: WeatherRepository) : ViewModel() {
    // Declara _state y state
    
    // Crea la funcion search(city: String)
    // Usa viewModelScope.launch { ... }
}',
                        ],
                    ],
                ],
                [
                    'type'  => 'summary',
                    'title' => 'Conclusión',
                    'bullets' => [
                        'Han dominado la arquitectura recomendada por Google.',
                        'Ahora pueden construir apps robustas, testeables y que no crashean al rotar la pantalla.',
                        '¡El siguiente paso es dominar las corrutinas avanzadas!',
                    ],
                ],
            ],
        ];
    }
}
