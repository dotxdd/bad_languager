<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrelloController extends Controller
{
    public function redirectToTrello()
    {
        $apiKey = env('TRELLO_API_KEY');
        $redirectUri = env('TRELLO_REDIRECT_URI');
        $authorizeUrl = "https://trello.com/1/authorize?response_type=token&key={$apiKey}&redirect_uri={$redirectUri}&scope=read,write";

        return redirect()->away($authorizeUrl);
    }

    public function handleTrelloAuth(Request $request)
    {
        $token = $request->query('token');

        if ($token) {
            $user = Auth::user();

            if ($user) {
                $user->tr_key = $token;
                $user->save();

                return redirect('/connect-trello');
            } else {
                Log::error('User not authenticated');
                return response()->json(['error' => 'User not authenticated'], 401);
            }
        } else {
            Log::error('No token provided in the callback');
            return response()->json(['error' => 'No token provided'], 400);
        }
    }

    public function showRedirectPage()
    {
        return view('trello-redirect');
    }

    public function deleteTrelloConnection()
    {
        $user = Auth::user();

        if ($user) {
            $user->tr_key = null;
            $user->save();

            return redirect('/connect-trello');
        } else {
            Log::error('User not authenticated');
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    }
}

