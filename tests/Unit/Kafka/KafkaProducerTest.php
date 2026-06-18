<?php

namespace Tests\Unit\Kafka;

use App\Enums\PriorityEnum;
use App\Kafka\KafkaClient;
use App\Kafka\KafkaProducer;
use App\Kafka\KafkaTopics;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class KafkaProducerTest extends TestCase
{
    private KafkaProducer $producer;
    private KafkaClient $clientMock;

    protected function setUp(): void
    {
        $this->clientMock = m::mock(KafkaClient::class);

        // Получаем значения констант из класса KafkaTopics
        $lowTopic = KafkaTopics::LOW;
        $highTopic = KafkaTopics::HIGH;

        $this->producer = new KafkaProducer($this->clientMock);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    public function test_sends_message_to_low_priority_topic(): void
    {
        // Arrange - positive тест (low priority)
        $priority = PriorityEnum::LOW->value;
        $payload = ['mass_message_id' => 123, 'key' => 'value'];

        $producerMock = m::mock('RdKafka\Producer');
        $topicMock = m::mock(\RdKafka\Topic::class);

        // Используем строковые значения вместо констант KafkaClient
        $this->clientMock
            ->shouldReceive('producer')
            ->andReturn($producerMock);

        $producerMock
            ->shouldReceive('newTopic')
            ->with(KafkaTopics::LOW) // Строковое значение темы для low priority
            ->andReturn($topicMock);

        $topicMock
            ->shouldReceive('produce')
            ->once()
            ->with(
                -1, // RD_KAFKA_PARTITION_UA
                0,
                json_encode($payload),
                '123'
            );

        $producerMock
            ->shouldReceive('flush')
            ->with(5000);

        // Act
        $this->producer->send($priority, $payload);

        // Assert 
        $this->assertTrue(true);
    }

    public function test_sends_message_to_high_priority_topic(): void
    {
        // Arrange - positive тест (high priority)
        $priority = PriorityEnum::HIGH->value;
        $payload = ['mass_message_id' => 456, 'key' => 'data'];

        $producerMock = m::mock('RdKafka\Producer');
        $topicMock = m::mock(\RdKafka\Topic::class);

        $this->clientMock
            ->shouldReceive('producer')
            ->andReturn($producerMock);

        $producerMock
            ->shouldReceive('newTopic')
            ->with(KafkaTopics::HIGH) // Строковое значение темы для high priority
            ->andReturn($topicMock);

        $topicMock
            ->shouldReceive('produce')
            ->once()
            ->with(
                -1,
                0,
                json_encode($payload),
                '456'
            );

        $producerMock
            ->shouldReceive('flush')
            ->with(5000);

        // Act
        $this->producer->send($priority, $payload);

        // Assert 
        $this->assertTrue(true);
    }

    public function test_get_topic_by_priority_returns_low_for_low(): void
    {
        // Arrange - positive тест (low priority)
        $result = $this->producer->getTopicByPriority(PriorityEnum::LOW->value);

        // Assert - positive тест: возвращает low topic для low priority
        $this->assertEquals(KafkaTopics::LOW, $result);
    }

    public function test_get_topic_by_priority_returns_high_for_medium(): void
    {
        // Arrange - negative тест (medium не является low)
        $result = $this->producer->getTopicByPriority('medium');

        // Assert - negative тест: возвращает high topic для medium priority
        $this->assertEquals(KafkaTopics::HIGH, $result);
    }

    public function test_get_topic_by_priority_returns_high_for_high(): void
    {
        // Arrange - positive тест (high priority)
        $result = $this->producer->getTopicByPriority(PriorityEnum::HIGH->value);

        // Assert - positive тест: возвращает high topic для high priority
        $this->assertEquals(KafkaTopics::HIGH, $result);
    }
}
