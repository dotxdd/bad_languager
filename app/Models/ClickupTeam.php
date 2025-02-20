<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickupTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'clickup_team_id', 'name', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
