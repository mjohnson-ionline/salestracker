<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveAccountsEmailFromDealsTable extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn('accounts_email');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            //
        });
    }
}
