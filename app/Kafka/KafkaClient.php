<?php

namespace App\Kafka;

use RdKafka\Conf;
use RdKafka\Producer;
use RdKafka\KafkaConsumer;

class KafkaClient
{
    public function producer(): Producer
    {
        $conf = new Conf();

        $conf->set('metadata.broker.list', config('kafka.brokers'));

        return new Producer($conf);
    }

    public function consumer(string $groupId, array $topics): KafkaConsumer
    {
        $conf = new Conf();

        $conf->set('group.id', $groupId);
        $conf->set('metadata.broker.list', config('kafka.brokers'));
        $conf->set('auto.offset.reset', 'earliest');

        $consumer = new KafkaConsumer($conf);
        $consumer->subscribe($topics);

        return $consumer;
    }
}
