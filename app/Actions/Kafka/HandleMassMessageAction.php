<?php

namespace App\Actions;

use App\Kafka\KafkaProducer;
use App\Services\MassMessageService;
use App\Data\MassMessageData;

final readonly class CreateMassMessageAction
{
    public function __construct(
        private MassMessageService      $massMessageService,
        private KafkaProducer $kafkaProducer,
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
