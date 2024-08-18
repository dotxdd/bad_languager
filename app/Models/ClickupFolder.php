<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClickupFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'clickup_folder_id', 'name', 'space_id'
    ];

    public function space()
    {
        return $this->belongsTo(ClickupSpace::class, 'space_id');
    }

    public function lists()
    {
        return $this->hasMany(ClickupList::class, 'folder_id');
    }
}
