<?php

namespace Tests\Unit\Jobs;

use App\Actions\SendRecipientAction;
use App\Data\SendMessageData;
use App\Jobs\SendRecipientJob;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Mockery as m;

class SendRecipientJobTest extends TestCase
{
    private SendMessageData $sendMessageData;

    protected function setUp(): void
    {
        Log::shouldReceive('warning')->andReturnSelf();
        Log::shouldReceive('debug')->andReturnSelf();
        Log::shouldReceive('info')->andReturnSelf();

        $this->sendMessageData = new SendMessageData(
            channel: 'push',
            message: 'Test message content here',
            recipientId: 123,
        );
    }

    protected function tearDown(): void
    {
        m::close();
    }

    public function test_handles_message_successfully_on_first_attempt(): void
    {
        // Arrange - positive тест (успешная отправка)
        $actionMock = m::mock(SendRecipientAction::class);
        $actionMock->shouldReceive('handle')
            ->once()
            ->with(m::any(), false)
            ->andReturn(true);

        $job = new SendRecipientJob(
            sendMessageData: $this->sendMessageData,
        );

        // Act
        $job->handle($actionMock);

        $this->assertTrue(true);
    }

}
