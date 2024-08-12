<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickupTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'clickup_task_id', 'name', 'description', 'status', 'list_id', 'assignee_id', 'creator_id', 'created_at', 'updated_at'
    ];

    public function team()
    {
        return $this->belongsTo(ClickupTeam::class, 'team_id');
    }

    public function space()
    {
        return $this->belongsTo(ClickupSpace::class, 'space_id');
    }

    public function folder()
    {
        return $this->belongsTo(ClickupFolder::class, 'folder_id');
    }

    public function list()
    {
        return $this->belongsTo(ClickupList::class, 'list_id');
    }

    public function workspace()
    {
        return $this->belongsTo(ClickupWorkspace::class, 'workspace_id');
    }
}
