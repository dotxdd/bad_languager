<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GPTService
{

    public function __construct()
    {
    }

    /**
     * Sends JSON containing tasks and comments to GPT-3.5 for content filtering.
     *
     * @param array $jsonPayload
     * @return array
     */
    public function checkContentForVulgarity(array $jsonPayload, $api_key): array
    {
        $requestPayload = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a content filter that detects vulgar and inappropriate language in tasks and comments. Respond with json with same structure but only with vulgar content.'
                ],
                [
                    'role' => 'user',
                    'content' => json_encode($jsonPayload)
                ],
            ],
            'temperature' => 0.7,
        ];
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$api_key}",
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', $requestPayload);

        return $response->json();
    }
}
