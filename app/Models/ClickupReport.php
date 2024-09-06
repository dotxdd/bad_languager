<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ClickupReport extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clickup_task_id',
        'clickup_comment_id',
        'user_id',
        'explict_message',
        'is_explict',

    ];

    protected $table='clickup_report_table';


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function comment()
    {
        return $this->belongsTo(ClickupComment::class);
    }
    public function task()
    {
        return $this->belongsTo(ClickupTask::class);
    }

}
