<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PriorityEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mass_messages', function (Blueprint $table) {
            $table->enum('priority', array_column(PriorityEnum::cases(), 'value'))
                ->default('normal')
                ->after('channel')
                ->comment('Приоритет доставки: low=маркетинг, normal=обычные уведомления, high=транзакционные');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mass_messages', function (Blueprint $table) {
            //
        });
    }
};
