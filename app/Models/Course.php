<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'icon',
        'title',
        'description',
        'level',
        'color',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function getLessonsCountAttribute()
    {
        return $this->lessons()->count();
    }

    /**
     * Students enrolled in this course (via pivot table).
     */
    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id')
                     ->withTimestamps();
    }
}
