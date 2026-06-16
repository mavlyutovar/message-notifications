<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;


#[OA\Info(
    title: 'Message Notifications API',
    version: '1.0.0',
    description: 'API для управления уведомлениями и массовой отправкой сообщений'
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Local server'
)]
class OpenApi
{
}