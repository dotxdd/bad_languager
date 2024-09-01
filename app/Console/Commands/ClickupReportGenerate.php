<?php

namespace App\Console\Commands;

use App\Models\ClickupComment;
use App\Models\ClickupTask;
use App\Models\User;
use App\Services\GPTService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClickupReportGenerate extends Command
{
    protected $signature = 'clickup:report-generate';
    protected $description = 'Fetch teams, spaces, folders, lists, users, and tasks for all users with a cu_key from ClickUp API';


    public function handle(): int
    {
        $users = User::whereNotNull('open_ai_key')->get();

        if ($users->isEmpty()) {
            $this->info('No users with an OpenAI API key found.');
            return 0; // Exit the command gracefully
        }

        foreach ($users as $user) {
            $this->info("Processing user: {$user->email}");
            $gptService = new GPTService($user->open_ai_key);

            $yesterday = Carbon::yesterday();

            $tasks = ClickupTask::whereDate('created_at', $yesterday)->get();

            $comments = ClickupComment::whereDate('created_at', $yesterday)->get();

            if ($tasks->isEmpty() && $comments->isEmpty()) {
                $this->info("No tasks or comments found for user: {$user->email}");
                continue;
            }

            $jsonPayload = [
                'tasks' => $tasks->map(function ($task) {
                    return [
                        'clickup_task_id' => $task->clickup_task_id,
                        'description' => $task->description,
                    ];
                })->toArray(),
                'comments' => $comments->map(function ($comment) {
                    return [
                        'clickup_comment_id' => $comment->clickup_comment_id,
                        'comment' => $comment->comment,
                    ];
                })->toArray(),
            ];

            try {
                $response = $gptService->checkContentForVulgarity($jsonPayload, $user->open_ai_key);

                $flaggedTasks = [];
                $flaggedComments = [];

                if (isset($response['choices'][0]['message']['content'])) {
                    $gptResult = json_decode($response['choices'][0]['message']['content'], true);

                    $flaggedTasks = $gptResult['tasks'] ?? [];
                    $flaggedComments = $gptResult['comments'] ?? [];

                    $this->displayFlaggedContent($user->email, $flaggedTasks, $flaggedComments);
                } else {
                    $this->warn("No valid response from GPT-3.5 for user: {$user->email}");
                }
            } catch (\Exception $e) {
                $this->error("Error processing user: {$user->email} - {$e->getMessage()}");
                \Log::error("Error processing user: {$user->email}", ['error' => $e->getMessage()]);
            }
        }

        return 0;
    }

    /**
     * Display flagged tasks and comments for a user.
     *
     * @param string $userEmail
     * @param array $tasks
     * @param array $comments
     * @return void
     */
    private function displayFlaggedContent(string $userEmail, array $tasks, array $comments): void
    {
        if (!empty($tasks)) {
            $this->info("Flagged Tasks for user: {$userEmail}");
            foreach ($tasks as $task) {
                $this->line("- Task ID: {$task['clickup_task_id']}, Description: {$task['description']}");
            }
        } else {
            $this->info("No flagged tasks for user: {$userEmail}");
        }

        if (!empty($comments)) {
            $this->info("Flagged Comments for user: {$userEmail}");
            foreach ($comments as $comment) {
                $this->line("- Comment ID: {$comment['clickup_comment_id']}, Comment: {$comment['comment']}");
            }
        } else {
            $this->info("No flagged comments for user: {$userEmail}");
        }
    }
}
