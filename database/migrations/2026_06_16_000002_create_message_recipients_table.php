<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\MessageRecipientStatusEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mass_message_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', array_column(MessageRecipientStatusEnum::cases(), 'value'))->default('queued');
            $table->integer('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['mass_message_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_recipients');
    }
};
