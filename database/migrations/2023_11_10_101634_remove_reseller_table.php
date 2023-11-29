<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveResellerTable extends Migration
{
    public function up(): void
    {
        Schema::table('reseller', function (Blueprint $table) {
            // drop the reseller table
            Schema::dropIfExists('reseller');
        });
    }

    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {
            //
        });
    }
}
