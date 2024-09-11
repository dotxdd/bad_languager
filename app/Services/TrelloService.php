<?php

namespace App\Services;

use App\Models\TrelloMember;
use App\Models\TrelloReport;
use App\Models\User;
use Carbon\Carbon;

class TrelloService
{
    public static function getToxicUsersRank(User $user)
    {
        $tasks = TrelloReport::where('trello_report_table.user_id', $user->id)
            ->where('trello_report_table.is_explict', 0)
        ->whereNotNull('trello_report_table.trello_card_id')
        ->join('trello_cards', 'trello_report_table.trello_card_id', '=', 'trello_cards.id')
            ->selectRaw('count(trello_report_table.id) as report_count, trello_cards.created_by as trello_user_id')
            ->groupBy('trello_cards.created_by')
            ->get();

        $comments = TrelloReport::where('trello_report_table.user_id', $user->id)
            ->where('trello_report_table.is_explict', 0)
            ->whereNotNull('trello_report_table.trello_comment_id')
        ->join('trello_comments', 'trello_report_table.trello_comment_id', '=', 'trello_comments.id')
            ->selectRaw('count(trello_report_table.id) as report_count, trello_comments.created_by as trello_user_id')
            ->groupBy('trello_comments.created_by')
            ->get();

        $merged = $tasks->concat($comments)
            ->groupBy('trello_user_id')
            ->map(function ($items, $key) {
                return [
                    'trello_user_id' => $key,
                    'total_report_count' => $items->sum('report_count')
                ];
            })
            ->values();
        $result = $merged->map(function ($item) {
            $trelloUser = TrelloMember::where('id', $item['trello_user_id'])->first();
            if ($trelloUser) {
                return [
                    'trello_user_id' => $item['trello_user_id'],
                    'total_report_count' => $item['total_report_count'],
                    'email' => $trelloUser->email,
                    'name' => $trelloUser->name
                ];
            }

            return null;
        })->filter();

        return $result;
    }

    public static function getToxicUsersRankMont(User $user, Carbon $date)
    {
        $tasks = TrelloReport::where('trello_report_table.user_id', $user->id)
            ->whereNotNull('trello_report_table.trello_card_id')
            ->where('trello_report_table.is_explict', 0)
            ->whereMonth('trello_report_table.created_at', $date->month)
            ->join('trello_cards', 'trello_report_table.trello_card_id', '=', 'trello_cards.id')
            ->selectRaw('count(trello_report_table.id) as report_count, trello_cards.created_by as trello_user_id')
            ->groupBy('trello_cards.created_by')
            ->get();

        $comments = TrelloReport::where('trello_report_table.user_id', $user->id)
            ->whereNotNull('trello_report_table.trello_comment_id')
            ->where('trello_report_table.is_explict', 0)
            ->whereMonth('trello_report_table.created_at', $date->month)
            ->join('trello_comments', 'trello_report_table.trello_comment_id', '=', 'trello_comments.id')
            ->selectRaw('count(trello_report_table.id) as report_count, trello_comments.created_by as trello_user_id')
            ->groupBy('trello_comments.created_by')
            ->get();

        $merged = $tasks->concat($comments)
            ->groupBy('trello_user_id')
            ->map(function ($items, $key) {
                return [
                    'trello_user_id' => $key,
                    'total_report_count' => $items->sum('report_count')
                ];
            })
            ->values();
        $result = $merged->map(function ($item) {
            $trelloUser = TrelloMember::where('id', $item['trello_user_id'])->first();
            if ($trelloUser) {
                return [
                    'trello_user_id' => $item['trello_user_id'],
                    'total_report_count' => $item['total_report_count'],
                    'email' => $trelloUser->email,
                    'name' => $trelloUser->name
                ];
            }

            return null;
        })->filter();

        return $result;
    }

    public static function getWholeTasksList(User $user)
    {
        return TrelloReport::where('trello_report_table.user_id', $user->id)
            ->whereNotNull('trello_report_table.trello_card_id')
            ->where('trello_report_table.is_explict', 0)
            ->leftJoin('trello_cards', 'trello_report_table.trello_card_id', '=', 'trello_cards.id')
            ->leftJoin('trello_boards', 'trello_cards.board_id', '=', 'trello_boards.id')
            ->leftJoin('trello_members', 'trello_cards.created_by', '=', 'trello_members.id')
            ->selectRaw('trello_cards.name as card_name, trello_cards.description as card_description
                , trello_boards.name as board_name,  trello_members.name, trello_cards.url')
            ->get();
    }
    public static function getWholeTasksListMonth(User $user, Carbon $date)
    {
        return TrelloReport::where('trello_report_table.user_id', $user->id)
            ->whereNotNull('trello_report_table.trello_card_id')
            ->where('trello_report_table.is_explict', 0)
            ->whereMonth('trello_report_table.created_at', $date->month)
            ->leftJoin('trello_cards', 'trello_report_table.trello_card_id', '=', 'trello_cards.id')
            ->leftJoin('trello_boards', 'trello_cards.board_id', '=', 'trello_boards.id')
            ->leftJoin('trello_members', 'trello_cards.created_by', '=', 'trello_members.id')
            ->selectRaw('trello_cards.name as card_name, trello_cards.description as card_description
                , trello_boards.name as board_name,  trello_members.name, trello_cards.url')
            ->get();
    }

    public static function getWholeTasksComments(User $user)
    {
        return TrelloReport::where('trello_report_table.user_id', $user->id)
            ->whereNotNull('trello_report_table.trello_comment_id')
            ->where('trello_report_table.is_explict', 0)
            ->leftJoin('trello_comments', 'trello_report_table.trello_comment_id', '=', 'trello_comments.id')
            ->leftJoin('trello_cards', 'trello_comments.card_id', '=', 'trello_cards.id')
            ->leftJoin('trello_boards', 'trello_cards.board_id', '=', 'trello_boards.id')
            ->leftJoin('trello_members', 'trello_comments.created_by', '=', 'trello_members.id')

            ->selectRaw('trello_comments.comment as comment, trello_cards.name as card_name,
                 trello_boards.name as board_name,  trello_members.name, trello_cards.url')
            ->get();
    }
    public static function getWholeTasksCommentsMonth(User $user, Carbon $date)
    {
        return TrelloReport::where('trello_report_table.user_id', $user->id)
            ->whereNotNull('trello_report_table.trello_comment_id')
            ->where('trello_report_table.is_explict', 0)
            ->whereMonth('trello_report_table.created_at', $date->month)
            ->leftJoin('trello_comments', 'trello_report_table.trello_comment_id', '=', 'trello_comments.id')
            ->leftJoin('trello_cards', 'trello_comments.card_id', '=', 'trello_cards.id')
            ->leftJoin('trello_boards', 'trello_cards.board_id', '=', 'trello_boards.id')
            ->leftJoin('trello_members', 'trello_comments.created_by', '=', 'trello_members.id')

            ->selectRaw('trello_comments.comment as comment, trello_cards.name as card_name,
                 trello_boards.name as board_name,  trello_members.name, trello_cards.url')
            ->get();
    }
}
