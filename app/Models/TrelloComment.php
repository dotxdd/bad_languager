<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrelloComment extends Model
{
    use HasFactory;

    protected $table = 'trello_comments';
    protected $fillable = ['trello_comment_id', 'card_id', 'comment', 'created_by'];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
    public function creator()
    {
        return $this->belongsTo(TrelloMember::class, 'created_by');
    }
}

