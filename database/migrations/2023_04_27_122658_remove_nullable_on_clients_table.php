<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveNullableOnClientsTable extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->string('company_name')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('status')->nullable(false)->change();
            $table->dropColumn('nimble_crm_id');
        });
    }

    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {
            //
        });
    }
}
