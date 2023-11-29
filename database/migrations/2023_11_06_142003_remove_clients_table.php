<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveClientsTable extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // remove the clients table
        });
    }

    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {
            //
        });
    }
}
