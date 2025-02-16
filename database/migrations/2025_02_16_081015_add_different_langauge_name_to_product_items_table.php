<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_items', static function (Blueprint $table) {
            $table->renameColumn('display_name', 'display_name_ka');
            $table->string('display_name_en')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('product_items', static function (Blueprint $table) {
            $table->renameColumn('display_name_ka', 'display_name');
            $table->dropColumn(['display_name_en']);
        });
    }
};
