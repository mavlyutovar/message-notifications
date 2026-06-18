<?php

namespace App\Jobs;

use App\Actions\SendRecipientAction;
use App\Data\SendMessageData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRecipientJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 10;

    public function __construct(
        public SendMessageData $sendMessageData,
    ) {}

    public function handle(SendRecipientAction $action): void
    {
        $isOutOfRetryAttempts = $this->tries - $this->attempts() == 0;
        $success = $action->handle($this->sendMessageData, $isOutOfRetryAttempts);

        if (!$success) {
            if ($isOutOfRetryAttempts) {
                throw new \RuntimeException('Send failed after 5 attempts');
            }
            $this->release($this->backoff);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job error: ' . $exception->getMessage());
    }
}
