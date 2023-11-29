<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPipedriveLogParsedToInvoiceToLogs extends Migration
{
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->boolean('pipedrive_log_parsed_to_invoice')->default(false)->after('pipedrive_duplicate_parsed');
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn('pipedrive_log_parsed_to_invoice');
        });
    }
}
