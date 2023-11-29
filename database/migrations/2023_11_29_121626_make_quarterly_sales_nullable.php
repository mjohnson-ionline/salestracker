<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeQuarterlySalesNullable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // make quarterly_sales nullable
            $table->decimal('quarterly_sales', 10, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
