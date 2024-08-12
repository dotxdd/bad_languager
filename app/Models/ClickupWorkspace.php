<?php
// app/Models/ClickUpWorkspace.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickupWorkspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'clickup_workspace_id', 'name', 'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function spaces()
    {
        return $this->hasMany(ClickupSpace::class, 'workspace_id');
    }
}
