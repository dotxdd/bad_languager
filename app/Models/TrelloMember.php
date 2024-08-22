<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloMember extends Model
{
    use HasFactory;
    protected $fillable=['name', 'trello_user_id', 'user_id', 'email'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
