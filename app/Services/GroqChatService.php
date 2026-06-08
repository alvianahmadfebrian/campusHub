<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GroqChatService
{
    public function complete(array $messages): array
    {
        $apiKey = config('services.groq.api_key');

        if (!$apiKey) {
            throw new RuntimeException('GROQ_API_KEY belum diatur di file .env.');
        }

        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post(config('services.groq.endpoint'), [
                'model' => config('services.groq.model'),
                'messages' => $messages,
                'max_tokens' => (int) config('services.groq.max_tokens', 1200),
                'temperature' => 0.4,
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'Groq request gagal: ' . $response->status() . ' - ' . $response->body()
            );
        }

        $json = $response->json();

        return [
            'content' => $json['choices'][0]['message']['content']
                ?? 'Maaf, saya belum bisa menjawab.',
            'model' => $json['model']
                ?? config('services.groq.model'),
        ];
    }
}
