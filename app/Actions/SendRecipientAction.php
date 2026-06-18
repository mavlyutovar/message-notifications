<?php

namespace App\Actions;

use App\Data\SendMessageData;
use App\Enums\MessageRecipientStatusEnum;
use App\Models\MessageRecipient;
use App\Services\MessageRecipientService;
use App\Services\NotificationService;

class SendRecipientAction
{
    public function __construct(
        private MessageRecipientService $messageRecipientService,
        private NotificationService  $notificationService,
    ) {}

    /**
     * Отправляет сообщение получателю.
     */
    public function handle(SendMessageData $messageData, int $isOutOfRetryAttempts): bool
    {
        $recipient = $this->messageRecipientService->find($messageData->recipientId);

        if (!$this->validate($recipient)) {
            return false;
        }

        $this->messageRecipientService->incrementAttempts($recipient->id);

        if ($isOutOfRetryAttempts){
            $this->messageRecipientService->updateStatus(
                $recipient->id,
                MessageRecipientStatusEnum::FAILED->value
            );
            return false;
        }

        try {
            $response = $this->notificationService->send(
                $recipient->massMessage->channel,
                $recipient->user,
                $messageData->message
            );

        } catch (\Throwable $e) {
            return false;
        }

        if (!$response->success) {
            return false;
        }

        $this->messageRecipientService->updateStatus(
            $recipient->id,
            MessageRecipientStatusEnum::DELIVERED->value
        );
        return true;
    }

    private function validate(MessageRecipient $recipient): bool
    {
        return $recipient
            && $recipient->user
            && $recipient->status === MessageRecipientStatusEnum::PROCESSING->value;
    }
}
