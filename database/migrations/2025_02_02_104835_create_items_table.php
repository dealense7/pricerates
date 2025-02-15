<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_items', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('barcode');
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->boolean('show')->default(false);
            $table->timestamps();
            $table->index('category_id');
            $table->index('name');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
