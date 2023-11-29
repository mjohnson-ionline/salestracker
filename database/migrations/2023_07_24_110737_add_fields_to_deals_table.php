<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToDealsTable extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn('xero_contact_id');
            $table->unsignedBigInteger('reseller_id')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn('reseller_id');
            $table->string('xero_contact_id')->nullable()->after('user_id');
        });
    }
}
