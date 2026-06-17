<?php

namespace Database\Factories;

use App\Models\MassMessage;
use App\Models\MessageRecipient;
use Illuminate\Database\Eloquent\Factories\Factory;


class MessageRecipientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mass_message_id' => MassMessage::factory(),
            'user_id' => fake()->numerify('########'),
            'status' => $this->faker->randomElement(['pending', 'sent', 'failed']),
            'attempts' => 0,
            'last_error' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'attempts' => fake()->numberBetween(1, 3),
        ]);
    }

    /**
     * Indicate that the message recipient failed to send.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'attempts' => fake()->numberBetween(1, 5),
            'last_error' => $this->faker->sentence(3),
        ]);
    }

    /**
     * Indicate that the message recipient is still pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'attempts' => 0,
        ]);
    }

    /**
     * Create a recipient for a specific mass message.
     */
    public function forMassMessage(int $massMessageId): static
    {
        return $this->state(fn (array $attributes) => [
            'mass_message_id' => $massMessageId,
        ]);
    }
}
