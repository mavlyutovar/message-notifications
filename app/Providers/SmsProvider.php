<?php

namespace App\Providers;

use App\Data\NotificationProviderResponseData;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SmsProvider implements ProviderInterface
{
    /**
     * Отправляет SMS сообщение.
     */
    public function send(User $user, string $message): NotificationProviderResponseData
    {
        try {
            // Здесь будет интеграция с провайдером SMS

            // Для примера — имитируем провальную отправку
            return new NotificationProviderResponseData(
                success: false,
                messageId: uniqid('sms_'),
                httpStatus: 404,
            );

        } catch (\Throwable $e) {
            Log::error('Ошибка отправки SMS', [
                'message' => substr($message, 0, 100),
                'error' => $e->getMessage(),
            ]);

            return new NotificationProviderResponseData(
                success: false,
                error: 'Ошибка отправки SMS: ' . $e->getMessage(),
            );
        }
    }
}
