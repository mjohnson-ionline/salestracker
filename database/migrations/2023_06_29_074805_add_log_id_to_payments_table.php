<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogIdToPaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('log_id')->nullable()->after('amount');
            $table->foreign('log_id')->references('id')->on('logs');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
}
