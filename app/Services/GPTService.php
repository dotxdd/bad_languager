<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GPTService
{
    const MAX_TOKENS = 4096; // Define the maximum token limit for a request
    const APPROX_CHAR_PER_TOKEN = 4; // Average character count per token

    public function __construct()
    {
    }

    /**
     * Sends JSON containing tasks and comments to GPT-3.5 for content filtering.
     *
     * @param array $jsonPayload
     * @param string $api_key
     * @return array
     */
    public function checkContentForVulgarity(array $jsonPayload, string $api_key): array
    {
        $maxChars = self::MAX_TOKENS * self::APPROX_CHAR_PER_TOKEN; // Max characters allowed per request
        $chunks = $this->splitPayload($jsonPayload, $maxChars); // Split the payload into manageable chunks

        $results = [];

        // Process each chunk
        foreach ($chunks as $chunk) {
            $response = $this->sendRequestToGPT($chunk, $api_key);
            if ($response && isset($response['choices'])) {
                $results = array_merge($results, $response['choices']);
            }
        }

        return $results;
    }

    /**
     * Splits the payload into smaller chunks to fit within the token limit.
     *
     * @param array $payload
     * @param int $maxChars
     * @return array
     */
    /**
     * Splits the payload into smaller chunks to fit within the token limit.
     *
     * @param array $payload
     * @param int $maxChars
     * @return array
     */
    private function splitPayload(array $payload, int $maxChars): array
    {
        $chunks = [];
        $currentChunk = [];
        $currentSize = 0;

        foreach ($payload as $key => $items) {
            $chunkItems = [];
            foreach ($items as $item) {
                $itemSize = strlen(json_encode([$key => [$item]]));

                // Check if adding the next item would exceed the max character limit
                if ($currentSize + $itemSize > $maxChars) {
                    if (!empty($currentChunk)) {
                        $chunks[] = $currentChunk; // Save the current chunk
                    }
                    $currentChunk = [$key => $chunkItems]; // Start a new chunk
                    $currentSize = 0; // Reset size counter
                    $chunkItems = [];
                }

                $chunkItems[] = $item; // Add the item to the chunk
                $currentSize += $itemSize; // Update the current size
            }

            if (!empty($chunkItems)) {
                $currentChunk[$key] = $chunkItems; // Add the remaining items
            }
        }

        // Add the last chunk if it's not empty
        if (!empty($currentChunk)) {
            $chunks[] = $currentChunk;
        }

        return $chunks;
    }


    /**
     * Sends a single chunk request to the GPT API.
     *
     * @param array $chunkPayload
     * @param string $api_key
     * @return array
     */
    private function sendRequestToGPT(array $chunkPayload, string $api_key): array
    {
        $requestPayload = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a content filter that detects vulgar and inappropriate language in tasks and comments. Respond with json with the same structure but only with vulgar content.'
                ],
                [
                    'role' => 'user',
                    'content' => json_encode($chunkPayload)
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
