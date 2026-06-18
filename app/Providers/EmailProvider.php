<?php

namespace App\Providers;

use App\Data\NotificationProviderResponseData;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmailProvider implements ProviderInterface
{
    /**
     * Отправляет email сообщение.
     */
    public function send(User $user, string $message): NotificationProviderResponseData
    {
        try {
            // Здесь будет интеграция с SMTP или другим сервисом отправки email

            // Для примера — имитируем провальную отправку
            return new NotificationProviderResponseData(
                success: false,
                messageId: uniqid('sms_'),
                httpStatus: 404,
            );

        } catch (\Throwable $e) {
            Log::error('Ошибка отправки email', [
                'message' => substr($message, 0, 100),
                'error' => $e->getMessage(),
            ]);

            return new NotificationProviderResponseData(
                success: false,
                error: 'Ошибка отправки email: ' . $e->getMessage(),
            );
        }
    }
}
