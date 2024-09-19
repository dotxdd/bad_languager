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

        return  response()->json(Services\ClickupService::getToxicUsersRank($user));
    }
    public static function getMonthlyWholeToxicUsers(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('month') ? Carbon::parse($request->input('month')) : Carbon::now();

        return  response()->json(Services\ClickupService::getToxicUsersRankMonth($user, $date));
    }

    public static function getWholeTasksData()
    {
        $user = Auth::user();

        return  response()->json(Services\ClickupService::getWholeTasksList($user));
    }
    public static function getWholeTasksDataMonth(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('month') ? Carbon::parse($request->input('month')) : Carbon::now();

        return  response()->json(Services\ClickupService::getWholeTasksListMonth($user, $date));
    }

    public static function getWholeCommentsData()
    {
        $user = Auth::user();

        return  response()->json(Services\ClickupService::getWholeTasksComments($user));
    }
    public static function getWholeCommentsMonth(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('month') ? Carbon::parse($request->input('month')) : Carbon::now();

        return  response()->json(Services\ClickupService::getWholeTasksCommentsMonth($user, $date));
    }

    public static function getUsers(Request $request)
    {
        $user = Auth::user();

        return  Services\ClickupService::getUsers($user);
    }
}
