<?php

namespace App\Console\Commands;

use App\Models\ClickupTeam;
use App\Models\ClickUpSpace; // Dodane do zarządzania przestrzeniami
use Illuminate\Console\Command;
use App\Models\User;
use GuzzleHttp\Client;

class FetchUserTeams extends Command
{
    protected $signature = 'fetch:user-teams';
    protected $description = 'Fetch teams and spaces for all users with a cu_key from ClickUp API';

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
        // Pobierz wszystkich użytkowników z cu_key
        $users = User::whereNotNull('cu_key')->get();

        foreach ($users as $user) {
            $accessToken = $user->cu_key;

            try {
                // Pobierz zespoły (teams)
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

                        // Zaktualizuj lub utwórz rekord zespołu
                        $clickupTeam = ClickupTeam::updateOrCreate(
                            ['clickup_team_id' => $team['id']],
                            ['name' => $team['name'], 'user_id' => $user->id]
                        );

                        // Pobierz przestrzenie (spaces) dla zespołu
                        $this->fetchSpacesForTeam($clickupTeam, $accessToken);
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error fetching teams for user ID: {$user->id} - " . $e->getMessage());
            }
        }
    }

    protected function fetchSpacesForTeam($team, $accessToken)
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

                    // Zaktualizuj lub utwórz rekord przestrzeni
                    ClickupSpace::updateOrCreate(
                        ['clickup_space_id' => $space['id']],
                        ['name' => $space['name'], 'team_id' => $team->id]
                    );
                }
            }
        } catch (\Exception $e) {
            $this->error("Error fetching spaces for team ID: {$team->clickup_team_id} - " . $e->getMessage());
        }
    }
}
