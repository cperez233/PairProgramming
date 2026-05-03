<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'lesson_id',
        'course_id',
        'grade',
        'feedback',
        'synced_to_moodle',
        'synced_at',
        'moodle_response',
    ];

    protected $casts = [
        'grade'            => 'decimal:1',
        'synced_to_moodle' => 'boolean',
        'synced_at'        => 'datetime',
        'moodle_response'  => 'array',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
