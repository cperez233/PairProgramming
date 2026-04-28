<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'duration',
        'icon',
        'order',
        'available',
        'content',
    ];

    protected $casts = [
        'available' => 'boolean',
        'content' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
