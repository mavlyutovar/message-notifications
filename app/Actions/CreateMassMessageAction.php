<?php

namespace App\Actions;

use App\Models\MassMessage as MassMessageModel;
use App\Services\MassMessageService;
use App\Services\MessageRecipientService;
use App\Services\UserService;
use App\Data\MassMessageData;
use App\Data\MassMessageRecipientData;
use App\Enums\MassMessageStatusEnum;
use Illuminate\Support\Facades\Log;

final readonly class CreateMassMessageAction
{
    public function __construct(
        private MassMessageService      $massMessageService,
        private MessageRecipientService $recipientService,
        private UserService             $userService,
    ) {}

    public function execute(MassMessageData $data): array
    {
        try {
            $massMessage = $this->massMessageService->create($data);

            foreach ($data->userIds as $userId) {
                try {
                    $user = $this->userService->find($userId);

                    if (!$user) {
                        continue;
                    }

                    $this->recipientService->create(
                        new MassMessageRecipientData(
                            mass_message_id: $massMessage->id,
                            user_id: $user->id,
                            status: 'queued',
                            attempts: 0,
                            last_error: null,
                        )
                    );
                } catch (\Exception $e) {
                    Log::warning('Ошибка при создании получателя', [
                        'user_id' => $userId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->massMessageService->updateStatus($massMessage->id, MassMessageStatusEnum::PROCESSING->value);

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
