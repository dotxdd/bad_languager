<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickupList extends Model
{
    use HasFactory;

    protected $fillable = [
        'clickup_list_id', 'name', 'folder_id'
    ];

    public function folder()
    {
        return $this->belongsTo(ClickupFolder::class, 'folder_id');
    }

    public function tasks()
    {
        return $this->hasMany(ClickupTask::class, 'list_id');
    }
}
