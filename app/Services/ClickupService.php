<?php

namespace App\Services;

use App\Models\ClickUpUser;

class ClickupService
{
    public static function getClickupUserId($id){
        return ClickUpUser::where('clickup_user_id', $id)->first()->id;
    }
    public static function getClickupListId($id){

    }
}
