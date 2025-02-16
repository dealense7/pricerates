<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_items', static function (Blueprint $table) {
            $table->boolean('has_image')->default(false)->index();
        });
    }

    public function down(): void
    {
        Schema::table('product_items', static function (Blueprint $table) {
            $table->dropColumn('has_image');
        });
    }
};
