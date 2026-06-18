<?php

namespace App\Providers;

use App\Data\NotificationProviderResponseData;
use App\Models\User;

/**
 * Интерфейс для провайдеров уведомлений.
 */
interface ProviderInterface
{
    /**
     * Отправить уведомление через данный канал.
     *
     * @param array $data Данные уведомления (user_id, channel, message и т.д.)
     * @return bool true если отправка успешна, false в противном случае
     */
    public function send(User $user, string $message): NotificationProviderResponseData;
}
