<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SupabaseAuthService
{
    private string $url;
    private string $anonKey;

    public function __construct()
    {
        $this->url = rtrim((string) config('services.supabase.url'), '/');
        $this->anonKey = (string) config('services.supabase.anon_key');

        if ($this->url === '' || $this->anonKey === '') {
            throw new RuntimeException('SUPABASE_URL atau SUPABASE_ANON_KEY belum diisi pada file .env.');
        }
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->url . '/auth/v1')
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'apikey' => $this->anonKey,
                'Authorization' => 'Bearer ' . $this->anonKey,
            ]);
    }

    public function signUp(string $email, string $password, array $metadata = []): array
    {
        return $this->handleResponse(
            $this->client()->post('/signup', [
                'email' => $email,
                'password' => $password,
                'data' => $metadata,
            ]),
            'Register gagal.'
        );
    }

    public function signIn(string $email, string $password): array
    {
        return $this->handleResponse(
            $this->client()->post('/token?grant_type=password', [
                'email' => $email,
                'password' => $password,
            ]),
            'Email atau password salah.'
        );
    }

    public function signOut(?string $accessToken): void
    {
        if (!$accessToken) {
            return;
        }

        $response = $this->client()
            ->withToken($accessToken)
            ->post('/logout');

        if ($response->failed() && $response->status() !== 401) {
            $this->handleResponse($response, 'Logout dari Supabase gagal.');
        }
    }

    private function handleResponse(Response $response, string $fallback): array
    {
        $payload = $response->json() ?? [];

        if ($response->failed()) {
            $message = $payload['msg']
                ?? $payload['message']
                ?? $payload['error_description']
                ?? $payload['error']
                ?? $fallback;

            throw new RuntimeException($message);
        }

        return $payload;
    }
}
