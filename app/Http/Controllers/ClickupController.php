<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClickupController extends Controller
{
    public function redirectToClickUp()
    {
        $clientId = env('CLICKUP_ID');
        $redirectUri = env('CLICKUP_REDIRECT_URI');
        $authorizeUrl = "https://app.clickup.com/api?client_id={$clientId}&redirect_uri={$redirectUri}";

        return redirect()->away($authorizeUrl);
    }

    public function handleCallback(Request $request)
    {
        $code = $request->query('code');

        if ($code) {
            $clientId = env('CLICKUP_ID');
            $clientSecret = env('CLICKUP_CLIENT_SECRET');

            $response = Http::post( "https://api.clickup.com/api/v2/oauth/token", [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
            ]);


            if ($response->successful()) {
                $body = $response->getBody();
                $data = json_decode($body, true);

                $accessToken = $data['access_token'];
                if ($accessToken) {
                    $user = Auth::user();

                    if ($user) {
                        $user->cu_key = $accessToken;
                        $user->save();

                        return redirect('/connect-clickup');
                    } else {
                        return response()->json(['error' => 'User not authenticated'], 401);
                    }
                } else {
                    return response()->json(['error' => 'No token provided'], 400);
                }
            } else {
                return response()->json(['error' => 'Failed to exchange code for token'], $response->status());
            }
        } else {
            return response()->json(['error' => 'No authorization code provided'], 400);
        }
    }

    public function deleteClickupConnection()
    {
        $user = Auth::user();

        if ($user) {
            $user->cu_key = null;
            $user->save();

            return redirect('/connect-clickup');
        } else {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    }
    public function fetchUserInfo()
    {
        $user = Auth::user();
        $accessToken = session('clickup_access_token');
        $client = new Client();

        $response = $client->get('https://api.clickup.com/api/v2/user', [
            'headers' => [
                'Authorization' =>  $user->cu_key,
            ],
        ]);

        $userInfo = json_decode($response->getBody(), true);

        // Assuming you want to fetch tasks for the first team
        $teamId = $userInfo['user']['teams'][0]['id'];

        // Save the teamId in session or database
        session(['clickup_team_id' => $teamId]);

        return redirect('/clickup/tasks');
    }
}
