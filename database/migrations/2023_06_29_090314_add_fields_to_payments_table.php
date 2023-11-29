<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('resourceId')->nullable()->after('id');
            $table->string('tenantId')->nullable()->after('resourceId');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('resourceId');
            $table->dropColumn('tenantId');
        });
    }
}
