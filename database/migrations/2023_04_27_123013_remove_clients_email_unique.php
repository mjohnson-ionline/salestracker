<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveClientsEmailUnique extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique('clients_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
}
