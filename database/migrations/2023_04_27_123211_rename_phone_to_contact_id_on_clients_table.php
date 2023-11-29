<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePhoneToContactIdOnClientsTable extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->renameColumn('phone', 'contact_id');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
}
