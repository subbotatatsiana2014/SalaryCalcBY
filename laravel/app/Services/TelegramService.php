<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }

    public function sendMessage(string $chatId, string $message): bool
    {
        try {
            $response = Http::post($this->apiUrl . 'sendMessage', [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful() && $response->json('ok')) {
                Log::info('Telegram message sent', ['chat_id' => $chatId]);
                return true;
            }

            Log::error('Telegram error', $response->json());
            return false;

        } catch (\Exception $e) {
            Log::error('Telegram exception: ' . $e->getMessage());
            return false;
        }
    }
}
