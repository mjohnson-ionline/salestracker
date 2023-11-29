<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMonthlyTargetToResellers extends Migration
{
    public function up(): void
    {
        Schema::table('resellers', function (Blueprint $table) {
            $table->decimal('monthly_target_once_off', 10, 2)->default(0);
            $table->decimal('monthly_target_recurring', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('resellers', function (Blueprint $table) {
            //
        });
    }
}
