<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickupComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id', 'user_id', 'comment', 'clickup_comment_id'
    ];

    public function task()
    {
        return $this->belongsTo(ClickupTask::class, 'clickup_task_id');
    }
    public function user()
    {
        return $this->belongsTo(ClickUpUser::class, 'user_id');
    }
}
