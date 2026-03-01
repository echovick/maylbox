<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('folder_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('uid');
            $table->string('message_id')->nullable();
            $table->string('in_reply_to')->nullable();
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->json('to');
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->json('reply_to')->nullable();
            $table->string('subject')->default('(No Subject)');
            $table->longText('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->string('snippet', 255)->default('');
            $table->timestamp('date')->nullable();
            $table->unsignedInteger('size')->default(0);
            $table->boolean('is_read')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_draft')->default(false);
            $table->boolean('has_attachments')->default(false);
            $table->json('attachments_meta')->nullable();
            $table->timestamps();

            $table->unique(['folder_id', 'uid']);
            $table->index(['email_account_id', 'folder_id', 'date']);
            $table->index('message_id');
            $table->index('in_reply_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
