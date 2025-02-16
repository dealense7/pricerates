<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('currency_rates', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained('currencies');
            $table->foreignId('provider_id')->constrained('currency_providers');
            $table->float('buy_rate');
            $table->float('sell_rate');
            $table->timestamp('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};
