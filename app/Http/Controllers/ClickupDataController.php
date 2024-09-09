<?php

namespace App\Http\Controllers;
use App\Services;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClickupDataController
{
    public static function getWholeToxicUsers()
    {
        $user = Auth::user();

        return Services\ClickupService::getToxicUsersRank($user);
    }
    public static function getMonthlyWholeToxicUsers(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('date') ? Carbon::parse($request->input('date')) : Carbon::now();


        return Services\ClickupService::getToxicUsersRankMonth($user, $date);
    }

    public static function getWholeTasksData()
    {
        $user = Auth::user();

        return Services\ClickupService::getWholeTasksList($user);
    }
    public static function getWholeTasksDataMonth(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('date') ? Carbon::parse($request->input('date')) : Carbon::now();

        return Services\ClickupService::getWholeTasksListMonth($user, $date);
    }

    public static function getWholeCommentsData()
    {
        $user = Auth::user();

        return Services\ClickupService::getWholeTasksComments($user);
    }
    public static function getWholeCommentsMonth(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('date') ? Carbon::parse($request->input('date')) : Carbon::now();

        return Services\ClickupService::getWholeTasksCommentsMonth($user, $date);
    }
}
