<?php

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
        Schema::table('product_prices', function (Blueprint $table) {
            $table->index(['status', 'item_id', 'current_price'], 'pp_status_item_price_idx');
            $table->index(['item_id', 'current_price'], 'pp_item_price_idx');
        });

        Schema::table('product_items', function (Blueprint $table) {
            $table->index(['id', 'has_image'], 'pi_id_image_idx');
        });
    }

    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropIndex('pp_status_item_price_idx');
            $table->dropIndex('pp_item_price_idx');
        });

        Schema::table('product_items', function (Blueprint $table) {
            $table->dropIndex('pi_id_image_idx');
        });
    }
};
