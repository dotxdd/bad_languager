<?php

namespace App\Console\Commands;

use App\Models\TrelloCard;
use App\Models\TrelloComment;
use App\Models\TrelloReport;
use App\Models\User;
use App\Services\GPTService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TrelloReportGenerate extends Command
{
    protected $signature = 'trello:report-generate';
    protected $description = 'Check Trello card descriptions and comments for inappropriate content using GPT-3.5 and save reports.';

    public function handle(): int
    {
        $users = User::whereNotNull('open_ai_key')->get();

        if ($users->isEmpty()) {
            return 0;
        }

        foreach ($users as $user) {
            $this->info("Processing user: {$user->email}");
            $gptService = new GPTService();

            $yesterday = Carbon::yesterday();

            $card = TrelloCard::whereDate('created_at', $yesterday)->get();

            $comments = TrelloComment::whereDate('created_at', $yesterday)->get();

            if ($card->isEmpty() && $comments->isEmpty()) {
                continue;
            }
            $jsonPayload = [
                'card' => $card->map(function ($card) {
                    return [
                        'trello_card_id' => $card->trello_id,
                        'description' => $card->description,
                    ];
                })->toArray(),
                'comments' => $comments->map(function ($comment) {
                    return [
                        'trello_comment_id' => $comment->trello_comment_id,
                        'comment' => $comment->comment,
                    ];
                })->toArray(),
            ];

            try {
                $response = $gptService->checkContentForVulgarity($jsonPayload, $user->open_ai_key);
                if (isset($response[0]['message']['content'])) {
                    $gptResult = json_decode($response[0]['message']['content'], true);

                    $flaggedCards = $gptResult['card'] ?? [];
                    $flaggedComments = $gptResult['comments'] ?? [];
                    $this->addReportData($user, $flaggedCards, $flaggedComments);
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
    private function addReportData(User $user, array $cards, array $comments): void
    {
        if (!empty($cards)) {
            foreach ($cards as $card) {
                $cardData = TrelloCard::where('trello_id',$card['trello_card_id'])->first();
                TrelloReport::updateOrCreate(
                    [
                        'trello_card_id' => $cardData->id,
                        'trello_comment_id' => null
                    ],
                    [
                        'user_id' => $user->id,
                        'explict_message' => $card['description'],
                        'is_explict' => 0
                    ]
                );
            }

        }



        if (!empty($comments)) {
            foreach ($comments as $comment) {

                $commentData = TrelloComment::where('trello_comment_id',$comment['trello_comment_id'])->first();

                TrelloReport::updateOrCreate(
                    [
                        'trello_comment_id' => $commentData->id,
                        'trello_card_id' => null
                    ],
                    [
                        'user_id' => $user->id,
                        'explict_message' => $comment['comment'],
                        'is_explict' => 0
                    ]
                );            }
        }
    }
}
