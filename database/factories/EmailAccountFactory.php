<?php

namespace Database\Factories;

use App\Models\EmailAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailAccount>
 */
class EmailAccountFactory extends Factory
{
    protected $model = EmailAccount::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'type' => 'imap',
            'provider' => 'custom',
            'imap_host' => 'imap.example.com',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'imap_username' => fake()->safeEmail(),
            'imap_password' => 'secret',
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 465,
            'smtp_encryption' => 'ssl',
            'smtp_username' => fake()->safeEmail(),
            'smtp_password' => 'secret',
            'is_default' => false,
            'is_active' => true,
            'sync_status' => 'synced',
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'sync_status' => 'pending',
        ]);
    }

    public function syncing(): static
    {
        return $this->state(fn (array $attributes) => [
            'sync_status' => 'syncing',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'sync_status' => 'failed',
            'sync_error' => 'Connection refused',
        ]);
    }
}
