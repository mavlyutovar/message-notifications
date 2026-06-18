<?php

namespace App\Kafka;

use Illuminate\Support\Facades\Log;

class KafkaConsumer
{
    public function __construct(
        private KafkaClient $client
    ) {}

    public function consume(
        string $groupId,
        array $topics,
        callable $handler
    ): void {
        $consumer = $this->client->consumer(
            $groupId,
            $topics
        );

        while (true) {
            try {
                $message = $consumer->consume(1000);

                if ($message === null) {
                    continue;
                }

                switch ($message->err) {

                    case RD_KAFKA_RESP_ERR_NO_ERROR:
                        echo "Processing new message...\n";
                        echo "Topic name: " . $message->topic_name . "\n";

                        $payload = json_decode($message->payload, true);

                        if (json_last_error() !== JSON_ERROR_NONE) {
                            echo "Invalid JSON payload\n";
                            continue 2;
                        }

                        $handler($payload);

                        break;

                    case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                        echo "No more messages; will wait for more\n";
                        break;

                    case RD_KAFKA_RESP_ERR__TIMED_OUT:
                        echo "Timed out\n";
                        break;

                    default:
                        echo "Kafka error: {$message->errstr()} (code: {$message->err})\n";
                        continue 2;
                }

            } catch (\Throwable $e) {
                echo
                    "Kafka consumer stopped: " .
                    $e->getMessage() .
                    "\n" .
                    $e->getTraceAsString();

                break;
                continue;
            }
        }
    }
}
