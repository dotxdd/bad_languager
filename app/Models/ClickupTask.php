<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickupTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'clickup_task_id', 'name', 'description', 'status', 'list_id', 'assignee_id', 'creator_id', 'created_at', 'updated_at', 'url'
    ];

    public function list()
    {
        return $this->belongsTo(ClickupList::class, 'list_id');
    }
}
