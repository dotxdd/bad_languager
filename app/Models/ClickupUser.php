<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickUpUser extends Model
{
    use HasFactory;
    protected $table = 'clickup_users';

    protected $fillable = [
        'clickup_user_id', 'username', 'email', 'user_id'
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
