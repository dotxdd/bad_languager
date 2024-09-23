<?php

namespace App\Http\Controllers;
use App\Models\ClickUpUser;
use App\Services;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClickupDataController
{
    public static function getWholeToxicUsers(Request $request)
    {
        $user = Auth::user();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        $users = $request->input('users');
        if (!$users){
            $users = ClickUpUser::pluck('id')->toArray();
        }

        return  response()->json(Services\ClickupService::getToxicUsersRank($user,$pageSize, $users));
    }
    public static function getMonthlyWholeToxicUsers(Request $request)
    {
        $user = Auth::user();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        $date = $request->has('month') ? Carbon::parse($request->input('month')) : Carbon::now();
        $users = $request->input('users');
        if (!$users){
            $users = ClickUpUser::pluck('id')->toArray();
        }
        return  response()->json(Services\ClickupService::getToxicUsersRankMonth($user,$pageSize, $date, $users));
    }

    public static function getWholeTasksData(Request $request)
    {
        $user = Auth::user();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        $users = $request->input('users');
        if (!$users){
            $users = ClickUpUser::pluck('id')->toArray();
        }

        return  response()->json(Services\ClickupService::getWholeTasksList($user,$pageSize, $users));
    }
    public static function getWholeTasksDataMonth(Request $request)
    {
        $user = Auth::user();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        $date = $request->has('month') ? Carbon::parse($request->input('month')) : Carbon::now();
        $users = $request->input('users');
        if (!$users){
            $users = ClickUpUser::pluck('id')->toArray();
        }

        return  response()->json(Services\ClickupService::getWholeTasksListMonth($user, $pageSize, $date, $users));
    }

    public static function getWholeCommentsData(Request $request)
    {
        $user = Auth::user();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        $users = $request->input('users');
        if (!$users){
            $users = ClickUpUser::pluck('id')->toArray();
        }

        return  response()->json(Services\ClickupService::getWholeTasksComments($user, $pageSize, $users));
    }
    public static function getWholeCommentsMonth(Request $request)
    {
        $user = Auth::user();
        $pageSize = $request->input('page_size', 10); // Default page size to 10

        $date = $request->has('month') ? Carbon::parse($request->input('month')) : Carbon::now();
        $users = $request->input('users');
        if (!$users){
            $users = ClickUpUser::pluck('id')->toArray();
        }

        return  response()->json(Services\ClickupService::getWholeTasksCommentsMonth($user, $pageSize, $date, $users));
    }

    public static function getUsers()
    {
        $user = Auth::user();

        return  Services\ClickupService::getUsers($user);
    }
}
