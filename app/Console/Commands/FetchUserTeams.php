<?php

namespace App\Console\Commands;

use App\Mail\DataDownloadedMail;
use App\Models\ClickupComment;
use App\Models\ClickupTeam;
use App\Models\ClickUpSpace;
use App\Models\ClickupFolder;
use App\Models\ClickupList;
use App\Models\ClickupTask;
use App\Models\ClickUpUser;
use App\Services\ClickupService;
use Illuminate\Console\Command;
use App\Models\User;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class FetchUserTeams extends Command
{
    protected $signature = 'fetch:user-teams';
    protected $description = 'Fetch teams, spaces, folders, lists, users, and tasks for all users with a cu_key from ClickUp API';

    protected $client;

    public function __construct()
    {
        parent::__construct();

        $this->client = new Client([
            'base_uri' => 'https://api.clickup.com/api/v2/',
        ]);
    }

    public function handle()
    {
        $users = User::whereNotNull('cu_key')->get();


        foreach ($users as $user) {
            $accessToken = $user->cu_key;
            $user_id = $user->id;
            try {
                $response = $this->client->request('GET', 'team', [
                    'headers' => [
                        'Authorization' => $accessToken,
                    ],
                ]);

                $teams = json_decode($response->getBody(), true);

                if (empty($teams['teams'])) {
                    $this->info("No teams found for user ID: {$user->id}");
                } else {
                    foreach ($teams['teams'] as $team) {
                        $this->info("User ID: {$user->id}, Team ID: {$team['id']}, Name: {$team['name']}");

                        $clickupTeam = ClickupTeam::updateOrCreate(
                            ['clickup_team_id' => $team['id']],
                            ['name' => $team['name'], 'user_id' => $user->id]
                        );

                        $this->fetchSpacesForTeam($clickupTeam, $accessToken, $user_id);
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error fetching teams for user ID: {$user->id} - " . $e->getMessage());
            }
            if ($user->is_downloaded_clickup_mail) {

                Mail::to($user->email)->send(new DataDownloadedMail(
                    $user,
                    'ClickUp',

                ));
            }
        }
    }

    protected function fetchSpacesForTeam($team, $accessToken, $user_id)
    {
        try {
            $response = $this->client->request('GET', "team/{$team->clickup_team_id}/space", [
                'headers' => [
                    'Authorization' => $accessToken,
                ],
            ]);

            $spaces = json_decode($response->getBody(), true);

            if (empty($spaces['spaces'])) {
                $this->info("No spaces found for team ID: {$team->clickup_team_id}");
            } else {
                foreach ($spaces['spaces'] as $space) {
                    $this->info("Team ID: {$team->clickup_team_id}, Space ID: {$space['id']}, Name: {$space['name']}");

                    $clickupSpace = ClickupSpace::updateOrCreate(
                        ['clickup_space_id' => $space['id']],
                        ['name' => $space['name'], 'team_id' => $team->id]
                    );

                    $this->fetchFoldersForSpace($clickupSpace, $accessToken, $user_id);
                }
            }
        } catch (\Exception $e) {
            $this->error("Error fetching spaces for team ID: {$team->clickup_team_id} - " . $e->getMessage());
        }
    }

    protected function fetchFoldersForSpace($space, $accessToken, $user_id)
    {
        try {
            $response = $this->client->request('GET', "space/{$space->clickup_space_id}/folder", [
                'headers' => [
                    'Authorization' => $accessToken,
                ],
            ]);

            $folders = json_decode($response->getBody(), true);

            if (empty($folders['folders'])) {
                $this->info("No folders found for space ID: {$space->clickup_space_id}");
            } else {
                foreach ($folders['folders'] as $folder) {
                    $this->info("Space ID: {$space->clickup_space_id}, Folder ID: {$folder['id']}, Name: {$folder['name']}");

                    $clickupFolder = ClickupFolder::updateOrCreate(
                        ['clickup_folder_id' => $folder['id']],
                        ['name' => $folder['name'], 'space_id' => $space->id]
                    );

                    $this->fetchListsForFolder($clickupFolder, $accessToken, $user_id);
                }
            }
        } catch (\Exception $e) {
            $this->error("Error fetching folders for space ID: {$space->clickup_space_id} - " . $e->getMessage());
        }
    }

    protected function fetchListsForFolder($folder, $accessToken, $user_id)
    {
        try {
            $response = $this->client->request('GET', "folder/{$folder->clickup_folder_id}/list", [
                'headers' => [
                    'Authorization' => $accessToken,
                ],
            ]);

            $lists = json_decode($response->getBody(), true);

            if (empty($lists['lists'])) {
                $this->info("No lists found for folder ID: {$folder->clickup_folder_id}");
            } else {
                foreach ($lists['lists'] as $list) {
                    $this->info("Folder ID: {$folder->clickup_folder_id}, List ID: {$list['id']}, Name: {$list['name']}");

                    $clickupList = ClickupList::updateOrCreate(
                        ['clickup_list_id' => $list['id']],
                        ['name' => $list['name'], 'folder_id' => $folder->id]
                    );

                    $this->fetchUsersForList($clickupList, $accessToken, $user_id);

                    $this->fetchTasksForList($clickupList, $accessToken);
                }
            }
        } catch (\Exception $e) {
            $this->error("Error fetching lists for folder ID: {$folder->clickup_folder_id} - " . $e->getMessage());
        }
    }

    protected function fetchTasksForList($list, $accessToken)
    {
        try {
            $oneWeekAgo = Carbon::now()->subWeek()->startOfDay()->format('Y-m-d\TH:i:s\Z');
            $response = $this->client->request('GET', "list/{$list->clickup_list_id}/task", [
                'headers' => [
                    'Authorization' => $accessToken,
                ],
                'query' => [
                    'date_created_gt' => strtotime($oneWeekAgo) * 1000, // Przekonwertuj na milisekundy
                ],
            ]);

            $tasks = json_decode($response->getBody(), true);

            if (empty($tasks['tasks'])) {
                $this->info("No tasks found for list ID: {$list->clickup_list_id}");
            } else {
                foreach ($tasks['tasks'] as $task) {
                    $this->info("List ID: {$list->clickup_list_id}, Task ID: {$task['id']}, Name: {$task['name']}");

                    $clickupTask = ClickupTask::updateOrCreate(
                        ['clickup_task_id' => $task['id']],
                        [
                            'name' => $task['name'],
                            'description' => $task['description'] ?? '',
                            'status' => $task['status']['status'],
                            'url'=> $task['url'],
                            'list_id' => $list->id,
                            'assignee_id' => ClickupService::getClickupUserId( $task['assignees'][0]['id'] ?? null) ?? null,
                            'creator_id' => ClickupService::getClickupUserId($task['creator']['id'] ?? null) ?? null,
                            'created_at' => Carbon::createFromTimestampMs($task['date_created'])->toDateTimeString(),
                            'updated_at' => Carbon::createFromTimestampMs($task['date_updated'])->toDateTimeString()
                        ]
                    );
                    $this->fetchCommentsForTask($clickupTask, $accessToken);
                }
            }
        } catch (\Exception $e) {
            $this->error("Error fetching tasks for list ID: {$list->clickup_list_id} - " . $e->getMessage());
        }
    }

    protected function fetchUsersForList($list, $accessToken, $user_id)
    {
        try {
            $response = $this->client->request('GET', "list/{$list->clickup_list_id}/member", [
                'headers' => [
                    'Authorization' => $accessToken,
                ],
            ]);

            $users = json_decode($response->getBody(), true);
            if (empty($users['members'])) {
                $this->info("No users found for list ID: {$list->clickup_list_id}");
            } else {
                foreach ($users['members'] as $user) {
                    $this->info("List ID: {$list->clickup_list_id}, User ID: {$user['id']}, Username: {$user['username']}");

                    ClickUpUser::updateOrCreate(
                        ['clickup_user_id' => $user['id']],
                        [
                            'username' => $user['username'],
                            'email' => $user['email'] ?? '',
                            'user_id' => $user_id
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            $this->error("Error fetching users for list ID: {$list->clickup_list_id} - " . $e->getMessage());
        }
    }

    protected function fetchCommentsForTask($task, $accessToken)
    {
            try {
                $response = $this->client->request('GET', "task/{$task->clickup_task_id}/comment", [
                    'headers' => [
                        'Authorization' => $accessToken,
                    ],
                ]);

                $comments = json_decode($response->getBody(), true);
                if (empty($comments['comments'])) {
                    $this->info("No comments found for task ID: {$task->clickup_task_id}");
                    $hasMoreComments = false;
                } else {
                    foreach ($comments['comments'] as $comment) {
                        $this->info("Task ID: {$task->clickup_task_id}, Comment ID: {$comment['id']}, Commentor ID: {$comment['user']['id']}");

                        ClickupComment::updateOrCreate(
                            ['clickup_comment_id' => $comment['id']],
                            [
                                'task_id' => $task->id,
                                'user_id' => ClickupService::getClickupUserId($comment['user']['id']),
                                'comment' => $comment['comment_text']
                            ]
                        );
                    }

                }
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                if ($e->hasResponse() && $e->getResponse()->getStatusCode() == 429) {
                    $this->warn("Rate limit reached. Waiting for 60 seconds...");
                    sleep(60);
                } else {
                    $this->error("Error fetching comments for task ID: {$task->clickup_task_id} - " . $e->getMessage());
                }
            }

    }
}
