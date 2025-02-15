<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_urls', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('providers');
            $table->foreignId('store_id')->constrained('stores');
            $table->jsonb('meta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_urls');
    }
};
