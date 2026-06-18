<?php

namespace App\Console\Schedule;

use App\Actions\FinalizeMassMessageAction;
use Illuminate\Console\Command;
use App\Models\MassMessage;
use App\Services\MassMessageService;
use App\Repositories\MessageRecipientRepository;
use App\Enums\MassMessageStatusEnum;

class FinalizeMassMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:finalize {--batch-size=100 : Количество сообщений для обработки за один проход}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Финализирует статусы массовых отправок ';

    /**
     * Execute the console command.
     */
    public function handle(FinalizeMassMessageAction $action, int $batchSize = 100): int
    {
        $this->info('Начинаю финализацию массовых отправок...');

        $messages = MassMessage::where('status', MassMessageStatusEnum::PROCESSING->value)
            ->orderByDesc('created_at')
            ->chunk($batchSize, function ($messages) use ($action) {
                foreach ($messages as $message) {
                    $action->handle($message->id);
                }
            });

        return Command::SUCCESS;
    }
}
