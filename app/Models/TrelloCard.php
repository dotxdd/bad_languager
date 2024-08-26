<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloCard extends Model
{
    use HasFactory;

    protected $table = 'trello_cards';
    protected $fillable = ['trello_id', 'board_id', 'name', 'description', 'created_by', 'url'];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }
}

