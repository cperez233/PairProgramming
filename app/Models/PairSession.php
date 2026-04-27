<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PairSession extends Model
{
    protected $fillable = [
        'code',
        'driver',
        'navigator',
        'status',
        'thread_id',
        'chat_history',
        'code_content',
        'cursors',
    ];
    protected $casts = [
        'chat_history' => 'array',
        'cursors' => 'array',
    ];
}