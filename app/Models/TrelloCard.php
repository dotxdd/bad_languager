<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloCard extends Model
{
    use HasFactory;

    protected $fillable = ['trello_id', 'board_id', 'name', 'description'];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }}
