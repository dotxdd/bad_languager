<?php

namespace App\Http\Controllers;

use App\Models\TrelloMember;
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
        $users = $request->input('users');
        if (!$users){
            $users = TrelloMember::pluck('id')->toArray();
        }

        return response()->json(Services\TrelloService::getToxicUsersRank($user, $pageSize, $users));
    }

    public static function getMonthlyWholeToxicUsers(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('month') ? Carbon::parse($request->input('month')) : Carbon::now();
        $pageSize = $request->input('page_size', 10); // Default page size to 10
        $users = $request->input('users');
        if (!$users){
            $users = TrelloMember::pluck('id')->toArray();
        }

        return response()->json(Services\TrelloService::getToxicUsersRankMont($user, $date, $pageSize, $users));
    }

    public static function getWholeTasksData(Request $request)
    {
        $user = Auth::user();
        $pageSize = $request->input('page_size', 10); // Default page size to 10
        $users = $request->input('users');
        if (!$users){
            $users = TrelloMember::pluck('id')->toArray();
        }

        return response()->json(Services\TrelloService::getWholeTasksList($user, $pageSize, $users));
    }

    public static function getWholeTasksDataMonth(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('month') ? Carbon::parse($request->input('month')) : Carbon::now();
        $pageSize = $request->input('page_size', 10); // Default page size to 10
        $users = $request->input('users');
        if (!$users){
            $users = TrelloMember::pluck('id')->toArray();
        }

        return response()->json(Services\TrelloService::getWholeTasksListMonth($user, $date, $pageSize, $users));
    }

    public static function getWholeCommentsData(Request $request)
    {
        $user = Auth::user();
        $pageSize = $request->input('page_size', 10); // Default page size to 10
        $users = $request->input('users');
        if (!$users){
            $users = TrelloMember::pluck('id')->toArray();
        }
        return response()->json(Services\TrelloService::getWholeTasksComments($user, $pageSize, $users));
    }

    public static function getWholeCommentsMonth(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('month') ? Carbon::parse($request->input('month')) : Carbon::now();
        $pageSize = $request->input('page_size', 10); // Default page size to 10
        $users = $request->input('users');
        if (!$users){
            $users = TrelloMember::pluck('id')->toArray();
        }

        return response()->json(Services\TrelloService::getWholeTasksCommentsMonth($user, $date, $pageSize, $users));
    }

    public static function getMonthlyDataChart(Request $request)
    {
        $user = Auth::user();
        $date = $request->has('month') ? Carbon::parse($request->input('month')) : Carbon::now();

        return response()->json(Services\TrelloService::getMonthlyDataChart($user, $date));
    }
    public static function getWholeDataChart(Request $request)
    {
        $user = Auth::user();

        return response()->json(Services\TrelloService::getWholeDataChart($user));
    }
    public static function getMembers(Request $request)
    {
        $user = Auth::user();

        return Services\TrelloService::getAllMembers($user);
    }
}
