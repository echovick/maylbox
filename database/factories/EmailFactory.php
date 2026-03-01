<?php

namespace Database\Factories;

use App\Models\Email;
use App\Models\EmailAccount;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Email>
 */
class EmailFactory extends Factory
{
    protected $model = Email::class;

    public function definition(): array
    {
        return [
            'email_account_id' => EmailAccount::factory(),
            'folder_id' => Folder::factory(),
            'uid' => fake()->unique()->numberBetween(1, 999999),
            'message_id' => '<' . fake()->uuid() . '@example.com>',
            'from_email' => fake()->safeEmail(),
            'from_name' => fake()->name(),
            'to' => [['email' => fake()->safeEmail(), 'name' => fake()->name()]],
            'subject' => fake()->sentence(),
            'body_text' => fake()->paragraphs(3, true),
            'body_html' => '<p>' . fake()->paragraphs(3, true) . '</p>',
            'snippet' => fake()->text(200),
            'date' => fake()->dateTimeBetween('-30 days', 'now'),
            'size' => fake()->numberBetween(1000, 50000),
            'is_read' => false,
            'is_starred' => false,
            'is_draft' => false,
            'has_attachments' => false,
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    public function starred(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_starred' => true,
        ]);
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }
}
