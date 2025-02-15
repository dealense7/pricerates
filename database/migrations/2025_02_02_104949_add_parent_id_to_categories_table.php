<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', static function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('categories');
        });
    }

    public function down(): void
    {
        Schema::table('categories', static function (Blueprint $table) {
            //
        });
    }
};
