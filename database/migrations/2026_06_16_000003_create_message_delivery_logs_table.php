<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\MessageDeliveryLogProviderEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_recipient_id')->constrained()->onDelete('cascade');
            $table->enum('provider', array_column(MessageDeliveryLogProviderEnum::cases(), 'value'));
            $table->enum('status', ['success', 'failed']);
            $table->string('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['message_recipient_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_delivery_logs');
    }
};
