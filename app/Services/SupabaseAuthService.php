<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SupabaseAuthService
{
    private function baseUrl(): string
    {
        return rtrim((string) env('SUPABASE_URL'), '/');
    }

    private function headers(): array
    {
        $key = (string) env('SUPABASE_ANON_KEY');

        return [
            'apikey' => $key,
            'Authorization' => 'Bearer ' . $key,
            'Content-Type' => 'application/json',
        ];
    }

    public function signUp(string $email, string $password): array
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl() . '/auth/v1/signup', [
                'email' => $email,
                'password' => $password,
            ]);

        if ($response->failed()) {
            throw new RuntimeException($response->json('msg') ?? $response->json('error_description') ?? 'Register gagal.');
        }

        return $response->json();
    }

    public function signIn(string $email, string $password): array
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl() . '/auth/v1/token?grant_type=password', [
                'email' => $email,
                'password' => $password,
            ]);

        if ($response->failed()) {
            throw new RuntimeException($response->json('error_description') ?? $response->json('msg') ?? 'Email atau password salah.');
        }

        return $response->json();
    }
}
