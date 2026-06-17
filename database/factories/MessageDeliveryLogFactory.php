<?php

namespace Database\Factories;

use App\Models\MessageDeliveryLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MessageDeliveryLog>
 */
class MessageDeliveryLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message_recipient_id' => fake()->numerify('########'),
            'provider' => $this->faker->randomElement(['twilio', 'sendgrid', 'mailgun']),
            'status' => $this->faker->randomElement(['success', 'failed', 'pending']),
            'response_code' => null,
            'response_body' => null,
            'error_message' => null,
        ];
    }

    /**
     * Indicate that the delivery was successful.
     */
    public function success(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
            'response_code' => fake()->numberBetween(200, 299),
            'error_message' => null,
        ]);
    }

    /**
     * Indicate that the delivery failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'response_code' => fake()->numberBetween(400, 599),
            'error_message' => $this->faker->sentence(3),
        ]);
    }

    /**
     * Indicate that the delivery is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'response_code' => null,
            'error_message' => null,
        ]);
    }

    /**
     * Create a delivery log for a specific message recipient.
     */
    public function forRecipient(int $recipientId): static
    {
        return $this->state(fn (array $attributes) => [
            'message_recipient_id' => $recipientId,
        ]);
    }
}
