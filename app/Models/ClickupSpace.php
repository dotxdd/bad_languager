<?php

// app/Models/ClickUpSpace.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickupSpace extends Model
{
    use HasFactory;

    protected $fillable = [
        'clickup_space_id', 'name', 'team_id'
    ];

    public function teams()
    {
        return $this->belongsTo(ClickupTeam::class, 'team_id');
    }

    public function folders()
    {
        return $this->hasMany(ClickupFolder::class, 'space_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('team', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }
}
