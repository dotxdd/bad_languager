<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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
        // Get the token from the query parameters
        $token = $request->query('token');

        // Check if the token is present
        if ($token) {
            // Get the currently authenticated user
            $user = Auth::user();

            // Check if user is authenticated
            if ($user) {
                // Store the token in the user's tr_key attribute
                $user->tr_key = $token;
                $user->save();

                // Return the token in a JSON response
                return redirect('/connect-trello');
            } else {
                // Return an error response if no user is authenticated
                return response()->json(['error' => 'User not authenticated'], 401);
            }
        } else {
            // Return an error response if no token is provided
            return response()->json(['error' => 'No token provided'], 400);
        }

    }
    public function showRedirectPage()
    {
        return view('trello-redirect');
    }

    public function getAllCards()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Check if user is authenticated and has a token
        if ($user && $user->tr_key) {
            $apiKey = env('TRELLO_API_KEY');
            $token = $user->tr_key;

            // Make a request to Trello API to get the boards
            $response = Http::get("https://api.trello.com/1/members/me/boards", [
                'key' => $apiKey,
                'token' => $token,
            ]);

            // Check if the request was successful
            if ($response->successful()) {
                $boards = $response->json();
                $allCards = [];

                // Loop through each board and get its cards
                foreach ($boards as $board) {
                    $boardId = $board['id'];

                    // Make a request to Trello API to get the cards of the board
                    $cardsResponse = Http::get("https://api.trello.com/1/boards/{$boardId}/cards", [
                        'key' => $apiKey,
                        'token' => $token,
                    ]);

                    // Check if the cards request was successful
                    if ($cardsResponse->successful()) {
                        $cards = $cardsResponse->json();
                        $allCards = array_merge($allCards, $cards);
                        dd($allCards);
                    } else {
                        return response()->json(['error' => 'Failed to fetch cards for board ' . $boardId], 500);
                    }
                }

                // Return all cards in a JSON response
                return response()->json(['cards' => $allCards]);
            } else {
                // Return an error response if the API request failed
                return response()->json(['error' => 'Failed to fetch boards from Trello'], 500);
            }
        } else {
            // Return an error response if no user is authenticated or no token is stored
            return response()->json(['error' => 'User not authenticated or no token stored'], 401);
        }
    }

    public function deleteTrelloConnection()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Check if user is authenticated
        if ($user) {
            // Remove the token from the user's tr_key attribute
            $user->tr_key = null;
            $user->save();

            // Return a success response
            return redirect('/connect-trello');
        } else {
            // Return an error response if no user is authenticated
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    }
}
