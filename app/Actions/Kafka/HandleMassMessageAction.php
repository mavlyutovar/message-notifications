<?php

namespace App\Actions\Kafka;

use App\Data\SendMessageData;
use App\Enums\MassMessageStatusEnum;
use App\Enums\MessageRecipientStatusEnum;
use App\Jobs\SendRecipientJob;
use App\Services\MassMessageService;
use App\Services\MessageRecipientService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

final readonly class HandleMassMessageAction
{
    public function __construct(
        private MassMessageService $massMessageService,
        private MessageRecipientService $messageRecipientService,
    ) {}

    public function handle(int $messageId): void
    {
        $massMessage = $this->massMessageService->findWithRecipients($messageId);

        $this->massMessageService->updateStatus($massMessage->id, MassMessageStatusEnum::PROCESSING->value);
        $recipientIds = [];
        foreach ($massMessage->recipients as $recipient) {
            $recipientIds[] = $recipient->id;

            SendRecipientJob::dispatch(
                new SendMessageData(
                    channel: $massMessage->channel,
                    message: $massMessage->message,
                    recipientId: $recipient->id,
                )
            );
        }
        $this->messageRecipientService->updateStatuses($recipientIds, MessageRecipientStatusEnum::PROCESSING->value);
    }
}
