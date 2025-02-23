<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gas_rates', function (Blueprint $table) {
            $table->string('tag');
        });
    }

    public function down(): void
    {
        Schema::table('gas_rates', function (Blueprint $table) {
            $table->dropColumn('tag');
        });
    }
};
