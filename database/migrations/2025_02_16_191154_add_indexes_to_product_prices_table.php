<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_prices', static function (Blueprint $table) {
            $table->index(['created_at', 'item_id', 'current_price'], 'pp_created_at_item_id_current_price_idx');
        });
    }

    public function down(): void
    {
        Schema::table('product_prices', static function (Blueprint $table) {
            $table->dropIndex('pp_created_at_item_id_current_price_idx');
        });
    }
};
