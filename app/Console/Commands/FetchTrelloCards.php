<?php

namespace App\Console\Commands;

use App\Mail\DataDownloadedMail;
use App\Models\TrelloBoard;
use Illuminate\Console\Command;
use App\Models\TrelloCard;
use App\Models\TrelloMember;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use League\OAuth1\Client\Server\Trello;

class FetchTrelloCards extends Command
{
    protected $signature = 'trello:fetch-cards';
    protected $description = 'Fetch the last 100 cards and members from Trello for all users with a Trello key';

    protected $client;
    protected $rateLimitWaitTime = 60;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    public function handle()
    {
        $users = User::whereNotNull('tr_key')->get();

        foreach ($users as $user) {
            $this->info('Fetching cards and members for user: ' . $user->id);
            $this->fetchAndSaveData($user);
        }

        $this->info('Cards and members fetched and saved for all users.');
        return 0;
    }

    protected function fetchAndSaveData($user)
    {
        $this->fetchDataFromTrelloBoards($user->tr_key, $user->id);
    }

    protected function fetchDataFromTrelloBoards($key, $user_id)
    {
        $url = 'https://api.trello.com/1/members/me/boards';

        try {
            $response = $this->client->get($url, [
                'query' => [
                    'token' => $key,
                    'key' => env('TRELLO_API_KEY')
                ]
            ]);

            $boards = json_decode($response->getBody()->getContents(), true);
            Log::info('Trello API Response:', ['response' => $boards]);

            foreach ($boards as $boardData) {
                $board = TrelloBoard::updateOrCreate(
                    ['board_id' => $boardData['id'], 'user_id' => $user_id],
                    [
                        'name' => $boardData['name'],
                        'description' => $boardData['desc'] ?? '',
                    ]
                );

                $this->fetchAndSaveBoardMembers($boardData['id'], $key, $user_id);

                $this->fetchAndSaveBoardCards($boardData['id'], $key);
            }
            $user = User::find($user_id);
            if ($user->is_downloaded_trello_mail) {
                Mail::to($user->email)->send(new DataDownloadedMail(
                    $user,
                    'Trello',

                ));
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getCode() == 429) {

                $this->handleRateLimit();
                $this->fetchDataFromTrelloBoards($key, $user_id);
            } else {
                Log::error('Client Error: ' . $e->getMessage(), ['response' => $e->getResponse()->getBody()->getContents()]);
            }
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            Log::error('Server Error: ' . $e->getMessage(), ['response' => $e->getResponse()->getBody()->getContents()]);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('Connection Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General Error: ' . $e->getMessage());
        }
    }

    protected function fetchAndSaveBoardMembers($boardId, $key, $userId)
    {
        $url = "https://api.trello.com/1/boards/{$boardId}/members";
        $limit = 1000;

        try {
            $response = $this->client->get($url, [
                'query' => [
                    'token' => $key,
                    'key' => env('TRELLO_API_KEY'),
                    'limit' => $limit
                ]
            ]);

            $members = json_decode($response->getBody()->getContents(), true);
            Log::info('Trello Board Members:', ['board_id' => $boardId, 'members' => $members]);

            foreach ($members as $memberData) {
                TrelloMember::updateOrCreate(
                    ['trello_user_id' => $memberData['id'], 'user_id' => $userId],
                    [
                        'name' => $memberData['fullName'],
                        'email' => $memberData['email'] ?? null,
                    ]
                );
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getCode() == 429) {

                $this->handleRateLimit();
                $this->fetchAndSaveBoardMembers($boardId, $key, $userId);
            } else {
                Log::error('Client Error when fetching members: ' . $e->getMessage(), ['response' => $e->getResponse()->getBody()->getContents()]);
            }
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            Log::error('Server Error when fetching members: ' . $e->getMessage(), ['response' => $e->getResponse()->getBody()->getContents()]);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('Connection Error when fetching members: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General Error when fetching members: ' . $e->getMessage());
        }
    }

    protected function fetchAndSaveBoardCards($boardId, $key)
    {
        $tenDaysAgo = now()->subDays(10)->toIso8601String();
        $url = "https://api.trello.com/1/boards/{$boardId}/cards";
        $limit = 100;
        $page = 0;

        do {
            try {
                $response = $this->client->get($url, [
                    'query' => [
                        'token' => $key,
                        'key' => env('TRELLO_API_KEY'),
                        'since' => $tenDaysAgo,
                        'limit' => $limit,
                        'offset' => $page * $limit
                    ]
                ]);

                $cards = json_decode($response->getBody()->getContents(), true);
                Log::info('Trello Board Cards:', ['board_id' => $boardId, 'cards' => $cards]);

                foreach ($cards as $cardData) {
                    $board = TrelloBoard::where('board_id', $boardId)->first();

                    $cardId = $cardData['id'];
                    $actionsUrl = "https://api.trello.com/1/cards/{$cardId}/actions";
                    $actionsResponse = $this->client->get($actionsUrl, [
                        'query' => [
                            'token' => $key,
                            'key' => env('TRELLO_API_KEY'),
                            'filter' => 'createCard',
                            'limit' => 1
                        ]
                    ]);

                    $actions = json_decode($actionsResponse->getBody()->getContents(), true);
                    $creatorId = $actions[0]['idMemberCreator'] ?? null;
                    $creator = TrelloMember::where('trello_user_id', $creatorId)->first();

                    TrelloCard::updateOrCreate(
                        ['trello_id' => $cardData['id'], 'board_id' => $board->id],
                        [
                            'name' => $cardData['name'],
                            'description' => $cardData['desc'] ?? '',
                            'created_by' => $creator->id ?? null,
                            'url' => $cardData['url']
                        ]
                    );
                }

                $page++;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                if ($e->getCode() == 429) {
                    $this->handleRateLimit();
                } else {
                    Log::error('Client Error when fetching cards: ' . $e->getMessage(), ['response' => $e->getResponse()->getBody()->getContents()]);
                }
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                Log::error('Server Error when fetching cards: ' . $e->getMessage(), ['response' => $e->getResponse()->getBody()->getContents()]);
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                Log::error('Connection Error when fetching cards: ' . $e->getMessage());
            } catch (\Exception $e) {
                Log::error('General Error when fetching cards: ' . $e->getMessage());
            }
        } while (count($cards) === $limit);
    }


    protected function handleRateLimit()
    {
        Log::warning('Rate limit reached. Waiting for ' . $this->rateLimitWaitTime . ' seconds.');
        sleep($this->rateLimitWaitTime);
    }
}
