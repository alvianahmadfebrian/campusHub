<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenRouterChatService
{
    /**
     * @param array<int, array<string, mixed>> $messages
     * @param array<int, array<string, mixed>> $plugins
     * @return array{content: string, model: string|null}
     */
    public function complete(array $messages, array $plugins = []): array
    {
        $apiKey = (string) config('services.openrouter.api_key');
        $endpoint = (string) config('services.openrouter.endpoint');
        $models = array_values(array_filter(
            (array) config('services.openrouter.models', [])
        ));

        if ($apiKey === '') {
            throw new RuntimeException('OPENROUTER_API_KEY belum diisi pada file .env.');
        }

        if ($endpoint === '' || $models === []) {
            throw new RuntimeException('Konfigurasi OpenRouter belum lengkap.');
        }

        $headers = [];

        $siteUrl = (string) config('services.openrouter.site_url');
        $appTitle = (string) config('services.openrouter.app_title');

        if ($siteUrl !== '') {
            $headers['HTTP-Referer'] = $siteUrl;
        }

        if ($appTitle !== '') {
            $headers['X-OpenRouter-Title'] = $appTitle;
        }

        $payload = [
            'models' => $models,
            'messages' => $messages,
            'temperature' => 0.2,
            'max_tokens' => (int) config('services.openrouter.max_tokens', 1200),
        ];

        if ($plugins !== []) {
            $payload['plugins'] = $plugins;
        }

        $response = Http::acceptJson()
            ->asJson()
            ->withToken($apiKey)
            ->withHeaders($headers)
            ->timeout(120)
            ->post($endpoint, $payload);

        return $this->parseResponse($response);
    }

    /**
     * @return array{content: string, model: string|null}
     */
    private function parseResponse(Response $response): array
    {
        $payload = $response->json() ?? [];

        if ($response->failed()) {
            $message = data_get($payload, 'error.message')
                ?? data_get($payload, 'message')
                ?? 'OpenRouter gagal memproses permintaan.';

            throw new RuntimeException((string) $message);
        }

        $content = data_get($payload, 'choices.0.message.content');

        if (is_array($content)) {
            $content = collect($content)
                ->map(function ($item) {
                    if (is_array($item)) {
                        return (string) ($item['text'] ?? '');
                    }

                    return (string) $item;
                })
                ->implode("\n");
        }

        if (!is_string($content) || trim($content) === '') {
            throw new RuntimeException('OpenRouter tidak memberikan jawaban teks.');
        }

        return [
            'content' => trim($content),
            'model' => data_get($payload, 'model')
                ? (string) data_get($payload, 'model')
                : null,
        ];
    }
}
