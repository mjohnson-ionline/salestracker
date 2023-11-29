<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveAmountFromInvoicesTable extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->dropColumn('xero_invoice_id');
            $table->dropColumn('friendly_xero_id');
            $table->dropColumn('xero_contact_id');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
        });
    }
}
