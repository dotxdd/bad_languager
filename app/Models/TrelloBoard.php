<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloBoard extends Model
{
    use HasFactory;

    protected $table = 'trello_boards';
    protected $fillable = ['board_id', 'name', 'description', 'user_id'];

    public function cards()
    {
        return $this->hasMany(TrelloCard::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
