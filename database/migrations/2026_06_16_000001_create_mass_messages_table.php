<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\MassMessageStatusEnum;
use App\Enums\MassMessageChannelEnum;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mass_messages', function (Blueprint $table) {
            $table->id();
            $table->enum('channel', array_column(MassMessageChannelEnum::cases(), 'value'));
            $table->text('message');
            $table->enum('status', array_column(MassMessageStatusEnum::cases(), 'value'))->default(MassMessageStatusEnum::PENDING);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mass_messages');
    }
};
