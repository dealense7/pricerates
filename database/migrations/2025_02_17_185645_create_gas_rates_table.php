<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gas_rates', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('gas_providers');
            $table->string('name');
            $table->bigInteger('price');
            $table->timestamp('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gas_rates');
    }
};
