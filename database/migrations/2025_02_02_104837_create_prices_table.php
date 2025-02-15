<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_prices', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('product_items');
            $table->foreignId('provider_id')->constrained('providers');
            $table->foreignId('store_id')->constrained('stores');
            $table->biginteger('original_price')->nullable();
            $table->biginteger('current_price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
