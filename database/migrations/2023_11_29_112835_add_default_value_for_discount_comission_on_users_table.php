<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultValueForDiscountComissionOnUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // set the default for discount_comission to be 10
            $table->integer('discount_comission')->default(10)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
