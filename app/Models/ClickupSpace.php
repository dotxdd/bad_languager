<?php

// app/Models/ClickUpSpace.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickUpSpace extends Model
{
    use HasFactory;

    protected $fillable = [
        'clickup_space_id', 'name', 'workspace_id'
    ];

    public function workspace()
    {
        return $this->belongsTo(ClickupWorkspace::class, 'workspace_id');
    }

    public function folders()
    {
        return $this->hasMany(ClickupFolder::class, 'space_id');
    }
}
