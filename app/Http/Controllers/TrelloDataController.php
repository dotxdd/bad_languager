<?php

namespace App\Http\Controllers;

use App\Services;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrelloDataController
{
    public static function getWholeToxicUsers(Request $request)
    {
        $user = Auth::user();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        return Services\TrelloService::getToxicUsersRank($user, $pageSize);
    }

    public static function getMonthlyWholeToxicUsers(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        return Services\TrelloService::getToxicUsersRankMont($user, $date, $pageSize);
    }

    public static function getWholeTasksData(Request $request)
    {
        $user = Auth::user();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        return Services\TrelloService::getWholeTasksList($user, $pageSize);
    }

    public static function getWholeTasksDataMonth(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        return Services\TrelloService::getWholeTasksListMonth($user, $date, $pageSize);
    }

    public static function getWholeCommentsData(Request $request)
    {
        $user = Auth::user();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        return Services\TrelloService::getWholeTasksComments($user, $pageSize);
    }

    public static function getWholeCommentsMonth(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        return Services\TrelloService::getWholeTasksCommentsMonth($user, $date, $pageSize);
    }
}
