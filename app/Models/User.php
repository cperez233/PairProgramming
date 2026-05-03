<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'teacher_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Grades given by this user (as teacher).
     */
    public function gradesGiven(): HasMany
    {
        return $this->hasMany(Grade::class, 'teacher_id');
    }

    /**
     * Grades received by this user (as student).
     */
    public function gradesReceived(): HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    /**
     * Students assigned to this teacher.
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'teacher_id', 'id');
    }

    /**
     * Courses this student is enrolled in (via pivot table).
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id')
                     ->withTimestamps();
    }
}
