<?php

namespace App\Console\Commands;

use App\Actions\Kafka\HandleMassMessageAction;
use App\Kafka\KafkaConsumer;
use App\Kafka\KafkaTopics;
use Illuminate\Console\Command;

class KafkaHighPriorityCommand extends Command
{
    protected $signature = 'kafka:consume:high';

    public function __construct(
        private HandleMassMessageAction $action,
        private KafkaConsumer $consumer
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->consumer->consume(
            'notification-high-group',
            [KafkaTopics::HIGH],
            function (array $payload) {
                $this->action->handle($payload['mass_message_id']);
            }
        );
        return self::SUCCESS;
    }
}
