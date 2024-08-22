<?php

namespace App\Console\Commands;

use App\Models\TrelloBoard;
use Illuminate\Console\Command;
use App\Models\TrelloCard;
use App\Models\TrelloMember;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use League\OAuth1\Client\Server\Trello;

class FetchTrelloCards extends Command
{
    protected $signature = 'trello:fetch-cards';
    protected $description = 'Fetch the last 100 cards and members from Trello for all users with a Trello key';

    protected $client;

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
         $this->fetchDataFromTrelloBoards( $user->tr_key, $user->id);

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

               TrelloBoard::updateOrCreate(
                    ['board_id' => $boardData['id'], 'user_id' => $user_id],
                    [
                        'name' => $boardData['name'],
                        'description' => $boardData['desc'] ?? '',
                    ]
                );
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error('Client Error: ' . $e->getMessage(), ['response' => $e->getResponse()->getBody()->getContents()]);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            Log::error('Server Error: ' . $e->getMessage(), ['response' => $e->getResponse()->getBody()->getContents()]);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('Connection Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('General Error: ' . $e->getMessage());
        }
    }

}
