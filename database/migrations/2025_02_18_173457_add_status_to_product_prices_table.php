<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_prices', static function (Blueprint $table) {
            $table->boolean('status')->default(false);
            $table->index(['created_at', 'status'], 'pp_created_at_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('product_prices', static function (Blueprint $table) {
            $table->dropIndex('pp_created_at_status_idx');
            $table->dropColumn(['status']);
        });
    }
};
