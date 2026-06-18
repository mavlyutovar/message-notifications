<?php

namespace App\Actions;

use App\Models\MassMessage as MassMessageModel;
use App\Services\MassMessageService;
use App\Repositories\MessageRecipientRepository;
use App\Enums\MassMessageStatusEnum;
use Illuminate\Support\Facades\Log;

final readonly class FinalizeMassMessageAction
{
    public function __construct(
        private MassMessageService $service,
        private MessageRecipientRepository $recipientRepo,
    ) {}

    public function handle(int $massMessageId): void
    {
        $message = MassMessageModel::find($massMessageId);

        if (!$message) {
            Log::warning('Массовая отправка не найдена при финализации', [
                'id' => $massMessageId,
            ]);
            return;
        }

        if ($message->status !== MassMessageStatusEnum::PROCESSING->value) {
            Log::debug('Массовая отправка уже завершена, пропускаем финализацию', [
                'id' => $massMessageId,
                'status' => $message->status,
            ]);
            return;
        }

        $recipients = $this->recipientRepo->getByMassMessage($massMessageId);

        if (!$recipients) {
            Log::warning('Получатели не найдены', [
                'mass_message_id' => $massMessageId,
            ]);
            return;
        }

        $total = count($recipients);
        $sent = $recipients->where('status', 'sent')->count();
        $failed = $recipients->where('status', 'failed')->count();

        if ($sent + $failed < $total) {
            Log::debug('Доставка ещё не завершена, пропускаем финализацию', [
                'mass_message_id' => $massMessageId,
                'total' => $total,
                'sent' => $sent,
                'failed' => $failed,
            ]);
            return;
        }

        $exhausted = $recipients->where('status', 'failed')->where('attempts', '>=', 5)->count();

        if ($failed > 0 && $exhausted === $failed) {
            Log::warning('Массовая отправка завершена с ошибкой', [
                'id' => $massMessageId,
                'total_recipients' => $total,
                'sent' => $sent,
                'failed' => $failed,
            ]);

            $this->service->updateStatus($message->id, MassMessageStatusEnum::FAILED->value);
        } else {
            Log::info('Массовая отправка успешно завершена', [
                'id' => $massMessageId,
                'total_recipients' => $total,
                'sent' => $sent,
                'failed' => $failed,
            ]);

            $this->service->updateStatus($message->id, MassMessageStatusEnum::COMPLETED->value);
        }
    }
}
