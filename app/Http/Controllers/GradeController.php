<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Grade;
use App\Models\User;
use App\Services\MoodleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GradeController extends Controller
{
    /**
     * Display the grading dashboard — list of teacher's courses.
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user->isTeacher()) {
            abort(403);
        }

        $courses = $user->courses()
            ->withCount(['lessons', 'enrolledStudents'])
            ->get()
            ->map(function ($course) use ($user) {
                $studentCount = $course->enrolled_students_count;
                $totalGradeable = $course->lessons_count * $studentCount;
                $gradedCount = Grade::where('course_id', $course->id)
                    ->where('teacher_id', $user->id)
                    ->count();
                $syncedCount = Grade::where('course_id', $course->id)
                    ->where('teacher_id', $user->id)
                    ->where('synced_to_moodle', true)
                    ->count();

                $course->student_count = $studentCount;
                $course->graded_count = $gradedCount;
                $course->synced_count = $syncedCount;
                $course->total_gradeable = $totalGradeable;
                $course->graded_percent = $totalGradeable > 0
                    ? round(($gradedCount / $totalGradeable) * 100)
                    : 0;

                return $course;
            });

        return view('grades.index', compact('courses'));
    }

    /**
     * Show the grading table for a specific course.
     */
    public function show(int $courseId)
    {
        $user = auth()->user();

        if (!$user->isTeacher()) {
            abort(403);
        }

        $course = $user->courses()->with('lessons')->findOrFail($courseId);
        $lessons = $course->lessons()->orderBy('order')->get();
        $students = $course->enrolledStudents()->orderBy('name')->get();

        // Build grades matrix: student_id => lesson_id => grade
        $grades = Grade::where('course_id', $courseId)
            ->where('teacher_id', $user->id)
            ->get()
            ->groupBy('student_id')
            ->map(function ($studentGrades) {
                return $studentGrades->keyBy('lesson_id');
            });

        $moodleService = new MoodleService();
        $moodleConfigured = $moodleService->isConfigured();

        return view('grades.show', compact('course', 'lessons', 'students', 'grades', 'moodleConfigured'));
    }

    /**
     * Store or update a grade.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isTeacher()) {
            abort(403);
        }

        $request->validate([
            'student_id' => 'required|exists:users,id',
            'lesson_id'  => 'required|exists:lessons,id',
            'course_id'  => 'required|exists:courses,id',
            'grade'      => 'required|numeric|min:0|max:5',
            'feedback'   => 'nullable|string|max:2000',
        ]);

        // Verify teacher owns the course
        $course = $user->courses()->findOrFail($request->course_id);

        // Verify student is enrolled in this course
        $isEnrolled = $course->enrolledStudents()->where('users.id', $request->student_id)->exists();
        if (!$isEnrolled) {
            abort(403, 'Student is not enrolled in this course.');
        }

        $grade = Grade::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'lesson_id'  => $request->lesson_id,
            ],
            [
                'teacher_id'       => $user->id,
                'course_id'        => $request->course_id,
                'grade'            => $request->grade,
                'feedback'         => $request->feedback,
                'synced_to_moodle' => false, // Mark as unsynced after update
                'synced_at'        => null,
            ]
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'grade'   => $grade,
            ]);
        }

        return back()->with('success', 'Calificación guardada exitosamente.');
    }

    /**
     * Sync a single grade to Moodle.
     */
    public function sync(int $id)
    {
        $user = auth()->user();

        if (!$user->isTeacher()) {
            abort(403);
        }

        $grade = Grade::where('teacher_id', $user->id)->findOrFail($id);
        $moodleService = new MoodleService();

        if (!$moodleService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Moodle no está configurado. Verifica las variables MOODLE_URL y MOODLE_WS_TOKEN en .env.',
            ], 422);
        }

        $lesson = $grade->lesson;

        if (empty($lesson->moodle_assignment_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Esta lección no tiene un assignment de Moodle asociado.',
            ], 422);
        }

        // Get Moodle user ID by email
        $student = $grade->student;
        $moodleUserId = $moodleService->getUserByEmail($student->email);

        if (!$moodleUserId) {
            return response()->json([
                'success' => false,
                'message' => "No se encontró el usuario con email {$student->email} en Moodle.",
            ], 422);
        }

        // Send grade to Moodle
        $result = $moodleService->saveGrade(
            $lesson->moodle_assignment_id,
            $moodleUserId,
            $grade->grade,
            $grade->feedback ?? ''
        );

        if (isset($result['success']) && $result['success']) {
            $grade->update([
                'synced_to_moodle' => true,
                'synced_at'        => now(),
                'moodle_response'  => $result,
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'Calificación sincronizada con Moodle exitosamente.',
                'synced_at' => $grade->synced_at->format('d/m/Y H:i'),
            ]);
        }

        $grade->update([
            'moodle_response' => $result,
        ]);

        return response()->json([
            'success' => false,
            'message' => $result['error'] ?? 'Error desconocido al sincronizar con Moodle.',
        ], 422);
    }

    /**
     * Sync all unsynced grades for a course to Moodle.
     */
    public function syncAll(int $courseId)
    {
        $user = auth()->user();

        if (!$user->isTeacher()) {
            abort(403);
        }

        $course = $user->courses()->findOrFail($courseId);
        $moodleService = new MoodleService();

        if (!$moodleService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Moodle no está configurado.',
            ], 422);
        }

        $unsyncedGrades = Grade::where('course_id', $courseId)
            ->where('teacher_id', $user->id)
            ->where('synced_to_moodle', false)
            ->with(['student', 'lesson'])
            ->get();

        $results = ['synced' => 0, 'failed' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($unsyncedGrades as $grade) {
            if (empty($grade->lesson->moodle_assignment_id)) {
                $results['skipped']++;
                continue;
            }

            $moodleUserId = $moodleService->getUserByEmail($grade->student->email);

            if (!$moodleUserId) {
                $results['failed']++;
                $results['errors'][] = "Usuario no encontrado: {$grade->student->email}";
                continue;
            }

            $result = $moodleService->saveGrade(
                $grade->lesson->moodle_assignment_id,
                $moodleUserId,
                $grade->grade,
                $grade->feedback ?? ''
            );

            if (isset($result['success']) && $result['success']) {
                $grade->update([
                    'synced_to_moodle' => true,
                    'synced_at'        => now(),
                    'moodle_response'  => $result,
                ]);
                $results['synced']++;
            } else {
                $grade->update(['moodle_response' => $result]);
                $results['failed']++;
                $results['errors'][] = $result['error'] ?? "Error con {$grade->student->email}";
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'message' => "{$results['synced']} sincronizadas, {$results['failed']} fallidas, {$results['skipped']} omitidas.",
        ]);
    }
}
