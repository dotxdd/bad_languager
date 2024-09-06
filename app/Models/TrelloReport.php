<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TrelloReport extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'trello_card_id',
        'trello_comment_id',
        'user_id',
        'explict_message',
        'is_explict'

    ];

    protected $table='trello_report_table';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function card()
    {
        return $this->belongsTo(TrelloCard::class, 'trello_card_id');
    }

    public function comment()
    {
        return $this->belongsTo(TrelloComment::class, 'trello_comment_id');
    }


}
