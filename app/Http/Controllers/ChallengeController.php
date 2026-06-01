<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index()
    {
        $challenges = Course::withCount('lessons')->get();

        // Add enrollment status for logged-in students
        if (auth()->check() && auth()->user()->role === 'student') {
            $enrolledIds = auth()->user()->enrolledCourses()->pluck('courses.id')->toArray();
            foreach ($challenges as $challenge) {
                $challenge->is_enrolled = in_array($challenge->id, $enrolledIds);
            }
        }

        return view('challenges.index', compact('challenges'));
    }

    public function show(int $id)
    {
        $challenge = Course::findOrFail($id);
        $lessons = $challenge->lessons()->orderBy('order')->get();

        $isEnrolled = false;
        $enrolledCount = $challenge->enrolledStudents()->count();

        if (auth()->check() && auth()->user()->role === 'student') {
            $isEnrolled = $challenge->enrolledStudents()
                ->where('users.id', auth()->id())
                ->exists();
        }

        return view('challenges.show', compact('challenge', 'lessons', 'isEnrolled', 'enrolledCount'));
    }

    public function lesson(int $challengeId, int $lessonId)
    {
        $challenge = Course::findOrFail($challengeId);
        $lessons = $challenge->lessons()->orderBy('order')->get();
        $lesson = Lesson::where('course_id', $challengeId)->findOrFail($lessonId);

        return view('challenges.lesson', compact('challenge', 'lesson', 'lessons'));
    }

    /**
     * Enroll the current student in a course.
     */
    public function enroll(int $id)
    {
        $user = auth()->user();

        if ($user->role !== 'student') {
            abort(403);
        }

        $course = Course::findOrFail($id);
        $course->enrolledStudents()->syncWithoutDetaching([$user->id]);

        return back()->with('success', '¡Te has inscrito exitosamente al curso!');
    }

    /**
     * Unenroll the current student from a course.
     */
    public function unenroll(int $id)
    {
        $user = auth()->user();

        if ($user->role !== 'student') {
            abort(403);
        }

        $course = Course::findOrFail($id);
        $course->enrolledStudents()->detach($user->id);

        return back()->with('success', 'Te has desinscrito del curso.');
    }

    /**
     * Link the current student to a teacher using a code.
     */
    public function linkTeacher(Request $request)
    {
        $user = auth()->user();

        if ($user->role !== 'student') {
            abort(403);
        }

        $request->validate([
            'teacher_code' => 'required|string|max:50',
        ]);

        $teacher = \App\Models\User::where('role', 'teacher')
            ->where('teacher_id', $request->teacher_code)
            ->first();

        if (!$teacher) {
            return back()->withErrors([
                'teacher_code' => __('The entered Teacher Code is invalid.')
            ]);
        }

        $user->update([
            'teacher_id' => $teacher->id
        ]);

        return back()->with('success', __('Successfully linked with your teacher!') . ' (' . $teacher->name . ')');
    }

    /**
     * Unlink the current student from their teacher.
     */
    public function unlinkTeacher()
    {
        $user = auth()->user();

        if ($user->role !== 'student') {
            abort(403);
        }

        $user->update([
            'teacher_id' => null
        ]);

        return back()->with('success', __('Successfully unlinked from your teacher.'));
    }
}

