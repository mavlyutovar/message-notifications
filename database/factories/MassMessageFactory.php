<?php

namespace Database\Factories;

use App\Models\MassMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MassMessageFactory extends Factory
{

    public function definition(): array
    {
        return [
            'channel' => $this->faker->randomElement(['sms', 'email']),
            'message' => $this->faker->sentence(10),
            'idempotency_key' => STR::uuid(),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'failed']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the mass message has failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }
}
