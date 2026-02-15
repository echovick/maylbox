<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('type', ['oauth', 'imap'])->default('imap');
            $table->string('provider')->nullable(); // gmail, outlook, etc.

            // OAuth tokens (encrypted)
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();

            // IMAP/SMTP credentials (encrypted)
            $table->string('imap_host')->nullable();
            $table->integer('imap_port')->nullable();
            $table->string('imap_encryption')->default('ssl');
            $table->string('imap_username')->nullable();
            $table->text('imap_password')->nullable();

            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('smtp_encryption')->default('tls');
            $table->string('smtp_username')->nullable();
            $table->text('smtp_password')->nullable();

            // Status and settings
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('sync_status', ['pending', 'syncing', 'synced', 'failed'])->default('pending');
            $table->timestamp('last_synced_at')->nullable();
            $table->text('sync_error')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'is_default']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_accounts');
    }
};
