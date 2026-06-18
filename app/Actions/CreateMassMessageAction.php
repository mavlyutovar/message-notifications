<?php

namespace App\Actions;

use App\Enums\MessageRecipientStatusEnum;
use App\Kafka\KafkaProducer;
use App\Models\MessageRecipient;
use App\Services\MassMessageService;
use App\Data\MassMessageData;
use App\Services\MessageRecipientService;

final readonly class CreateMassMessageAction
{
    public function __construct(
        private KafkaProducer $kafkaProducer,
        private MassMessageService $massMessageService,
        private MessageRecipientService $messageRecipientService,
    ) {}

    public function handle(MassMessageData $data): array
    {
        if ($this->massMessageService->findByUuid($data->uuid)) {
            return [
                'success' => false,
                'error' => 'Сообщение уже сохранено',
            ];
        }

        try {
            $massMessage = $this->massMessageService->create($data);

            foreach ($data->userIds as $userId) {
                $recipients[] = [
                    'mass_message_id' => $massMessage->id,
                    'user_id' => $userId,
                    'status' => MessageRecipientStatusEnum::QUEUED->value,
                    'attempts' => 0,
                    'last_error' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $this->messageRecipientService->insertMany($recipients);

            if (isset($massMessage) && isset($massMessage->priority)) {
                $this->kafkaProducer->send($massMessage->priority, ['mass_message_id' => $massMessage->id]);
            }

            return [
                'success' => true,
                'data' => [
                    'id' => $massMessage->id,
                    'channel' => $massMessage->channel,
                    'priority' => $massMessage->priority,
                    'message_count' => count($data->userIds),
                    'status' => $massMessage->status,
                    'created_at' => $massMessage->created_at->toDateTimeString(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Не удалось создать массовую отправку: ' . $e->getMessage(),
            ];
        }
    }
}
