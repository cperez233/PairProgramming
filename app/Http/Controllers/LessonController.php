<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function edit($id)
    {
        if (auth()->user()->role !== 'teacher') {
            abort(403);
        }

        $lesson = Lesson::with('course')->findOrFail($id);
        
        // Ensure the teacher owns the course of this lesson
        if ($lesson->course->user_id !== auth()->id()) {
            abort(403);
        }

        return view('lessons.edit', compact('lesson'));
    }

    public function updateContent(Request $request, $id)
    {
        if (auth()->user()->role !== 'teacher') {
            abort(403);
        }

        $lesson = Lesson::with('course')->findOrFail($id);

        if ($lesson->course->user_id !== auth()->id()) {
            abort(403);
        }

        // We receive the structured JSON content from the frontend editor
        $contentData = json_decode($request->input('content_json'), true);

        $lesson->update([
            'content' => $contentData
        ]);

        return redirect()->route('courses.edit', $lesson->course_id)
            ->with('success', 'Lesson content updated successfully.');
    }
}
