<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_items', static function (Blueprint $table) {
            $table->string('unit_type')->nullable();
            $table->string('unit')->nullable();
            $table->string('display_name')->nullable();
            $table->string('brand_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('product_items', static function (Blueprint $table) {
            $table->dropcolumn([
                'unit_type',
                'display_name',
                'brand_name',
                'unit',
            ]);
        });
    }
};
