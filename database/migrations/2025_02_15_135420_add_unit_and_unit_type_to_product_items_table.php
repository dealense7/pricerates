<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->string('unit_type')->nullable();
            $table->string('unit')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('product_items', function (Blueprint $table) {
            $table->dropcolumn([
                'unit_type',
                'unit',
            ]);
        });
    }
};
