<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->boolean('xero_log_parsed_to_payment')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {

        });
    }
};
