<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddXeroInvoiceIdToPaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('xero_invoice_id')->nullable()->after('xero_contact_id');

            // remove transaction_id
            $table->dropColumn('transaction_id');

        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('xero_invoice_id');
        });
    }
}
