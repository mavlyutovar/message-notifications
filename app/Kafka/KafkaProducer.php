<?php

namespace App\Kafka;

use App\Enums\PriorityEnum;

class KafkaProducer
{
    public function __construct(
        private KafkaClient $client
    ) {}

    public function send(
        string $priority,
        array $payload
    ): void {
        $producer = $this->client->producer();

        $topic = $producer->newTopic(
            $this->getTopicByPriority($priority)
        );

        $topic->produce(
            RD_KAFKA_PARTITION_UA,
            0,
            json_encode($payload),
            $payload['mass_message_id']
        );

        $producer->flush(5000);
    }

    public function getTopicByPriority(string $priority): string
    {
        if($priority == PriorityEnum::LOW->value){
            return KafkaTopics::LOW;
        }
        return KafkaTopics::HIGH;
    }
}
