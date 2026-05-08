<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetTelegramChatId extends Command
{
    /**
     * Имя и описание комманд.
     *
     * @var string
     */
    protected $signature = 'telegram:get-chat-id';
    protected $description = 'Получите свой идентификатор чата в Telegram.';

    /**
     * Выполните команду в консоли.
     */
    public function handle()
    {
        $botToken = config('services.telegram.bot_token');

        if(!$botToken){
            $this->error('Нет токена!');
            return 1;
        }

        $response = Http::get("https://api.telegram.org/bot{$botToken}/getUpdates");

        if ($response->successful()) {
            $updates = $response->json('result', []);

            if (empty($updates)) {
                $this->warn('Ошибка! Попробуйте ещё раз!');
                return 1;
            }

            foreach ($updates as $update) {
                if (isset($update['message']['chat']['id'])) {
                    $chatId = $update['message']['chat']['id'];
                    $username = $update['message']['chat']['username'] ?? 'No username';
                    $firstName = $update['message']['chat']['first_name'] ?? '';
                    $lastName = $update['message']['chat']['last_name'] ?? '';

                    $this->line("User: {$firstName} {$lastName} (@{$username})");
                    $this->info("Chat ID: {$chatId}");
                }
            }

            $this->info('Добавьте строку в файл .env: TELEGRAM_ADMIN_CHAT_ID=' . $updates[0]['message']['chat']['id']);
        } else {
            $this->error('Не удалось получить обновления. Проверьте свой токен бота.');
        }

        return 0;
    }
}
