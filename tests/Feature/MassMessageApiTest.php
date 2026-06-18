<?php

namespace Tests\Feature;

use App\Enums\MassMessageStatusEnum;
use App\Enums\PriorityEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MassMessageApiTest extends TestCase
{
    public function test_it_can_send_sms_mass_message(): void
    {
        $user = User::factory()->create(['phone' => '+7 (900) 123-45-67']);

        $response = $this->postJson('/api/v1/mass-messages/send', [
            'channel' => 'sms',
            'message' => 'Привет! Это тестовое SMS сообщение.',
            'user_ids' => [$user->id],
            'uuid' => STR::uuid(),
            'priority' => PriorityEnum::LOW->value,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'channel',
                    'message_count',
                    'status',
                    'created_at',
                ],
            ]);

        $this->assertDatabaseHas('mass_messages', [
            'id' => $response->json('data.id'),
            'channel' => 'sms',
            'message' => 'Привет! Это тестовое SMS сообщение.',
        ]);

        $this->assertDatabaseCount('mass_messages', 1);
        $this->assertDatabaseHas('message_recipients', [
            'user_id' => $user->id,
            'mass_message_id' => $response->json('data.id'),
            'status' => 'queued',
        ]);
    }

    public function test_it_can_send_email_mass_message(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/v1/mass-messages/send', [
            'channel' => 'email',
            'message' => 'Привет! Это тестовое email сообщение.',
            'user_ids' => [$user->id],
            'uuid' => STR::uuid(),
            'priority' => PriorityEnum::LOW->value,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'channel',
                    'message_count',
                    'status',
                    'created_at',
                ],
            ]);

        $this->assertDatabaseHas('mass_messages', [
            'id' => $response->json('data.id'),
            'channel' => 'email',
            'message' => 'Привет! Это тестовое email сообщение.',
        ]);

        $this->assertDatabaseCount('mass_messages', 1);
        $this->assertDatabaseHas('message_recipients', [
            'user_id' => $user->id,
            'mass_message_id' => $response->json('data.id'),
            'status' => 'queued',
        ]);
    }

    public function test_it_can_get_mass_message_status(): void
    {
        $user = User::factory()->create();

        $sendResponse = $this->postJson('/api/v1/mass-messages/send', [
            'channel' => 'sms',
            'message' => 'Тестовое сообщение.',
            'user_ids' => [$user->id],
            'uuid' => STR::uuid(),
            'priority' => PriorityEnum::LOW->value,
        ]);

        $massMessageId = $sendResponse->json('data.id');

        // Проверка структуры ответа отправки
        $sendResponse->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'channel',
                    'message_count',
                    'status',
                    'created_at',
                ],
            ]);

        $response = $this->getJson("/api/v1/mass-messages/{$massMessageId}/status");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'channel',
                    'message',
                    'user_ids_count',
                    'status',
                    'created_at',
                ],
            ]);

        $this->assertDatabaseHas('mass_messages', [
            'id' => $massMessageId,
            'channel' => 'sms',
            'message' => 'Тестовое сообщение.',
        ]);
    }

    public function test_it_can_list_mass_messages(): void
    {
        User::factory()->count(3)->create();

        foreach (['sms', 'email'] as $channel) {
            for ($i = 0; $i < 2; $i++) {
                $this->postJson('/api/v1/mass-messages/send', [
                    'channel' => $channel,
                    'message' => "Тестовое сообщение {$channel} #{$i}",
                    'user_ids' => [User::factory()->create()->id],
                    'uuid' => STR::uuid(),
                    'priority' => PriorityEnum::LOW->value,
                ]);
            }
        }

        $response = $this->getJson('/api/v1/mass-messages?channel=sms&limit=50');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'channel',
                            'message_preview',
                            'user_ids_count',
                            'status',
                            'created_at',
                        ],
                    ],
                    'total',
                ],
            ]);

        $this->assertDatabaseHas('mass_messages', [
            'channel' => 'sms',
        ]);
    }

    public function test_it_filters_mass_messages_by_channel(): void
    {
        User::factory()->create(['email' => 'sms@example.com']);
        $user = User::factory()->create(['phone' => '+7 (900) 123-45-67']);

        $this->postJson('/api/v1/mass-messages/send', [
            'channel' => 'sms',
            'message' => 'SMS сообщение.',
            'user_ids' => [$user->id],
            'uuid' => STR::uuid(),
            'priority' => PriorityEnum::LOW->value,
        ]);

        $emailUser = User::factory()->create(['email' => 'email@example.com']);

        $this->postJson('/api/v1/mass-messages/send', [
            'channel' => 'email',
            'message' => 'Email сообщение.',
            'user_ids' => [$emailUser->id],
            'uuid' => STR::uuid(),
            'priority' => PriorityEnum::LOW->value,
        ]);

        $response = $this->getJson('/api/v1/mass-messages?channel=sms&limit=50');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'channel',
                            'message_preview',
                            'user_ids_count',
                            'status',
                            'created_at',
                        ],
                    ],
                    'total',
                ],
            ])
            ->assertJsonCount(1, 'data.items');

        $response = $this->getJson('/api/v1/mass-messages?channel=email&limit=50');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'channel',
                            'message_preview',
                            'user_ids_count',
                            'status',
                            'created_at',
                        ],
                    ],
                    'total',
                ],
            ])
            ->assertJsonCount(1, 'data.items');

        $this->assertDatabaseHas('mass_messages', [
            'channel' => 'sms',
        ]);
    }
}
