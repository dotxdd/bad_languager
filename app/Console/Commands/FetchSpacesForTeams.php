<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ClickupTeam;
use App\Models\ClickUpSpace;
use GuzzleHttp\Client;

class FetchSpacesForAllUserTeams extends Command
{
    protected $signature = 'fetch:spaces-for-all-user-teams';
    protected $description = 'Fetch all spaces for teams of all users from ClickUp API';

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

            $teams = ClickupTeam::forUser($user->id)->get();

            foreach ($teams as $team) {
                try {
                    $response = $this->client->request('GET', "team/{$team->clickup_team_id}/space", [
                        'headers' => [
                            'Authorization' => $user->cu_key,
                        ],
                    ]);

                    $spaces = json_decode($response->getBody(), true);

                    if (isset($spaces['spaces'])) {
                        foreach ($spaces['spaces'] as $space) {

                            $spaceModel = ClickUpSpace::updateOrCreate(
                                ['clickup_space_id' => $space['id']],
                                ['name' => $space['name'], 'team_id' => $team->id]
                            );

                            $this->info("Fetched space for team ID: {$team->clickup_team_id}, Space ID: {$spaceModel->clickup_space_id}, Name: {$spaceModel->name}");
                        }
                    } else {
                        $this->warn("No space data found for team ID: {$team->clickup_team_id}");
                    }
                } catch (\Exception $e) {
                    $this->error("Error fetching spaces for team ID: {$team->clickup_team_id} - " . $e->getMessage());
                }
            }
        }
    }
}
