<?php

namespace Database\Factories;

use App\Models\EmailAccount;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Folder>
 */
class FolderFactory extends Factory
{
    protected $model = Folder::class;

    public function definition(): array
    {
        return [
            'email_account_id' => EmailAccount::factory(),
            'name' => 'INBOX',
            'type' => 'inbox',
            'remote_name' => 'INBOX',
            'unread_count' => 0,
            'total_count' => 0,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Sent',
            'type' => 'sent',
            'remote_name' => 'Sent',
        ]);
    }

    public function trash(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Trash',
            'type' => 'trash',
            'remote_name' => 'Trash',
        ]);
    }

    public function drafts(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Drafts',
            'type' => 'drafts',
            'remote_name' => 'Drafts',
        ]);
    }
}
