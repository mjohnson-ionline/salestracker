<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropClientsTable extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // ignore foreign key constraints
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('clients');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
}
