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
        return view('challenges.index', compact('challenges'));
    }

    public function show(int $id)
    {
        $challenge = Course::findOrFail($id);
        $lessons = $challenge->lessons()->orderBy('order')->get();

        return view('challenges.show', compact('challenge', 'lessons'));
    }

    public function lesson(int $challengeId, int $lessonId)
    {
        $challenge = Course::findOrFail($challengeId);
        $lessons = $challenge->lessons()->orderBy('order')->get();
        $lesson = Lesson::where('course_id', $challengeId)->findOrFail($lessonId);

        return view('challenges.lesson', compact('challenge', 'lesson', 'lessons'));
    }
}
