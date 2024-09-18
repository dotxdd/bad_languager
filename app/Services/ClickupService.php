<?php

namespace App\Services;

use App\Models\ClickupReport;
use App\Models\ClickUpUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class ClickupService
{
    public static function getClickupUserId($id){
        return ClickUpUser::where('clickup_user_id', $id)->first()->id;
    }
    public static function getClickupListId($id){

    }
    public static function getToxicUsersRank(User $user, $pageSize = 10)
    {
        $tasks = ClickupReport::where('clickup_report_table.user_id', $user->id)
        ->whereNotNull('clickup_report_table.clickup_task_id')
        ->where('clickup_report_table.is_explict', 0)
            ->join('clickup_tasks', 'clickup_report_table.clickup_task_id', '=', 'clickup_tasks.id')
            ->selectRaw('count(clickup_report_table.id) as report_count, clickup_tasks.creator_id as clickup_user_id')
            ->groupBy('clickup_tasks.creator_id')
            ->get();

        $comments = ClickupReport::where('clickup_report_table.user_id', $user->id)
        ->whereNotNull('clickup_report_table.clickup_comment_id')
            ->where('clickup_report_table.is_explict', 0)
        ->join('clickup_comments', 'clickup_report_table.clickup_comment_id', '=', 'clickup_comments.id')
            ->selectRaw('count(clickup_report_table.id) as report_count, clickup_comments.user_id as clickup_user_id')
            ->groupBy('clickup_comments.user_id')
            ->get();

        $merged = $tasks->concat($comments)
            ->groupBy('clickup_user_id')
            ->map(function ($items, $key) {
                return [
                    'clickup_user_id' => $key,
                    'total_report_count' => $items->sum('report_count')
                ];
            })
            ->values();
        $result = $merged->map(function ($item) {
            $clickupUser = ClickUpUser::where('id', $item['clickup_user_id'])->first();
            if ($clickupUser) {
                return [
                    'clickup_user_id' => $item['clickup_user_id'],
                    'total_report_count' => $item['total_report_count'],
                    'email' => $clickupUser->email,
                    'name' => $clickupUser->username
                ];
            }

            return null;
        })->filter();

        $currentPage = Paginator::resolveCurrentPage();
        $itemCollection = new Collection($result);
        $currentItems = $itemCollection->slice(($currentPage - 1) * $pageSize, $pageSize)->all();
        $paginatedItems = new LengthAwarePaginator($currentItems, $itemCollection->count(), $pageSize, $currentPage, [
            'path' => Paginator::resolveCurrentPath()
        ]);

        return $paginatedItems;    }

    public static function getToxicUsersRankMonth(User $user, Carbon $date, $pageSize = 10)
    {
        $tasks = ClickupReport::where('clickup_report_table.user_id', $user->id)
            ->whereNotNull('clickup_report_table.clickup_task_id')
            ->whereRaw("DATE_FORMAT(clickup_report_table.created_at, '%Y-%m') = ?", [$date->format('Y-m')])
            ->where('clickup_report_table.is_explict', 0)
            ->join('clickup_tasks', 'clickup_report_table.clickup_task_id', '=', 'clickup_tasks.id')
            ->selectRaw('count(clickup_report_table.id) as report_count, clickup_tasks.creator_id as clickup_user_id')
            ->groupBy('clickup_tasks.creator_id')
            ->get();

        $comments = ClickupReport::where('clickup_report_table.user_id', $user->id)
            ->whereNotNull('clickup_report_table.clickup_comment_id')
            ->whereRaw("DATE_FORMAT(clickup_report_table.created_at, '%Y-%m') = ?", [$date->format('Y-m')])
            ->where('clickup_report_table.is_explict', 0)
            ->join('clickup_comments', 'clickup_report_table.clickup_comment_id', '=', 'clickup_comments.id')
            ->selectRaw('count(clickup_report_table.id) as report_count, clickup_comments.user_id as clickup_user_id')
            ->groupBy('clickup_comments.user_id')
            ->get();

        $merged = $tasks->concat($comments)
            ->groupBy('clickup_user_id')
            ->map(function ($items, $key) {
                return [
                    'clickup_user_id' => $key,
                    'total_report_count' => $items->sum('report_count')
                ];
            })
            ->values();
        $result = $merged->map(function ($item) {
            $clickupUser = ClickUpUser::where('id', $item['clickup_user_id'])->first();
            if ($clickupUser) {
                return [
                    'clickup_user_id' => $item['clickup_user_id'],
                    'total_report_count' => $item['total_report_count'],
                    'email' => $clickupUser->email,
                    'name' => $clickupUser->username
                ];
            }

            return null;
        })->filter();

        $currentPage = Paginator::resolveCurrentPage();
        $itemCollection = new Collection($result);
        $currentItems = $itemCollection->slice(($currentPage - 1) * $pageSize, $pageSize)->all();
        $paginatedItems = new LengthAwarePaginator($currentItems, $itemCollection->count(), $pageSize, $currentPage, [
            'path' => Paginator::resolveCurrentPath()
        ]);

        return $paginatedItems;    }

     public static function getWholeTasksList(User $user, $pageSize = 10)
        {
            return ClickupReport::where('clickup_report_table.user_id', $user->id)
                ->whereNotNull('clickup_report_table.clickup_task_id')
                ->where('clickup_report_table.is_explict', 0)
                ->leftJoin('clickup_tasks', 'clickup_report_table.clickup_task_id', '=', 'clickup_tasks.id')
                ->leftJoin('clickup_lists', 'clickup_tasks.list_id', '=', 'clickup_lists.id')
                ->leftJoin('clickup_folders', 'clickup_lists.folder_id', '=', 'clickup_folders.id')
                ->leftJoin('clickup_spaces', 'clickup_folders.space_id', '=', 'clickup_spaces.id')
                ->leftJoin('clickup_users', 'clickup_tasks.creator_id', '=', 'clickup_users.id')
                ->selectRaw('clickup_tasks.name as task_name, clickup_tasks.description as task_description
                , clickup_lists.name as list_name, clickup_folders.name as folder_name,
                clickup_spaces.name as space_name, clickup_users.username, clickup_tasks.url')
                ->paginate($pageSize);
        }
    public static function getWholeTasksListMonth(User $user, Carbon $date, $pageSize = 10)
    {
        return ClickupReport::where('clickup_report_table.user_id', $user->id)
            ->whereNotNull('clickup_report_table.clickup_task_id')
            ->where('clickup_report_table.is_explict', 0)
            ->whereRaw("DATE_FORMAT(clickup_report_table.created_at, '%Y-%m') = ?", [$date->format('Y-m')])
            ->leftJoin('clickup_tasks', 'clickup_report_table.clickup_task_id', '=', 'clickup_tasks.id')
            ->leftJoin('clickup_lists', 'clickup_tasks.list_id', '=', 'clickup_lists.id')
            ->leftJoin('clickup_folders', 'clickup_lists.folder_id', '=', 'clickup_folders.id')
            ->leftJoin('clickup_spaces', 'clickup_folders.space_id', '=', 'clickup_spaces.id')
            ->leftJoin('clickup_users', 'clickup_tasks.creator_id', '=', 'clickup_users.id')
            ->selectRaw('clickup_tasks.name as task_name, clickup_tasks.description as task_description
            , clickup_lists.name as list_name, clickup_folders.name as folder_name,
            clickup_spaces.name as space_name, clickup_users.username, clickup_tasks.url')
            ->paginate($pageSize);
    }

    public static function getWholeTasksComments(User $user, $pageSize = 10)
    {
        return ClickupReport::where('clickup_report_table.user_id', $user->id)
            ->whereNotNull('clickup_report_table.clickup_comment_id')
            ->where('clickup_report_table.is_explict', 0)
            ->leftJoin('clickup_comments', 'clickup_report_table.clickup_comment_id', '=', 'clickup_comments.id')
            ->leftJoin('clickup_tasks', 'clickup_comments.task_id', '=', 'clickup_tasks.id')
            ->leftJoin('clickup_lists', 'clickup_tasks.list_id', '=', 'clickup_lists.id')
            ->leftJoin('clickup_folders', 'clickup_lists.folder_id', '=', 'clickup_folders.id')
            ->leftJoin('clickup_spaces', 'clickup_folders.space_id', '=', 'clickup_spaces.id')
            ->leftJoin('clickup_users', 'clickup_comments.user_id', '=', 'clickup_users.id')
            ->selectRaw('clickup_tasks.name as task_name, clickup_comments.comment as task_comments
                , clickup_lists.name as list_name, clickup_folders.name as folder_name,
                clickup_spaces.name as space_name, clickup_users.username, clickup_tasks.url')
            ->paginate($pageSize);
    }
    public static function getWholeTasksCommentsMonth(User $user, Carbon $date, $pageSize = 10)
    {
        return ClickupReport::where('clickup_report_table.user_id', $user->id)
            ->whereNotNull('clickup_report_table.clickup_comment_id')
            ->where('clickup_report_table.is_explict', 0)
            ->whereRaw("DATE_FORMAT(clickup_report_table.created_at, '%Y-%m') = ?", [$date->format('Y-m')])
            ->leftJoin('clickup_comments', 'clickup_report_table.clickup_comment_id', '=', 'clickup_comments.id')
            ->leftJoin('clickup_tasks', 'clickup_comments.task_id', '=', 'clickup_tasks.id')
            ->leftJoin('clickup_lists', 'clickup_tasks.list_id', '=', 'clickup_lists.id')
            ->leftJoin('clickup_folders', 'clickup_lists.folder_id', '=', 'clickup_folders.id')
            ->leftJoin('clickup_spaces', 'clickup_folders.space_id', '=', 'clickup_spaces.id')
            ->leftJoin('clickup_users', 'clickup_comments.user_id', '=', 'clickup_users.id')
            ->selectRaw('clickup_tasks.name as task_name, clickup_comments.comment as task_comments
            , clickup_lists.name as list_name, clickup_folders.name as folder_name,
            clickup_spaces.name as space_name, clickup_users.username, clickup_tasks.url')
            ->paginate($pageSize);
    }
}
