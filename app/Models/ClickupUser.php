<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickUpUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'clickup_user_id', 'username', 'email', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

