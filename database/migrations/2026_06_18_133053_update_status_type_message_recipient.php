<?php

use App\Enums\MessageRecipientStatusEnum;
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
        Schema::table('message_recipients', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('message_recipients', function (Blueprint $table) {
            $table->string('status')->default(MessageRecipientStatusEnum::SYSTEM_FAILED->value)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message_recipients', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('message_recipients', function (Blueprint $table) {
            $table->enum('status', array_column(MessageRecipientStatusEnum::cases(), 'value'))
                  ->default(MessageRecipientStatusEnum::SYSTEM_FAILED->value)
                  ->after('user_id');
        });
    }
};
