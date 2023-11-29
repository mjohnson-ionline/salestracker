<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldOnDealsTable extends Migration
{
    public function up(): void
    {
        // change the field 'invoice_to_email_address' to 'accounts_email'
        Schema::table('deals', function (Blueprint $table) {
            $table->renameColumn('invoice_to_email_address', 'accounts_email');
        });
    }

    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {
            $table->renameColumn('accounts_email', 'invoice_to_email_address');
        });
    }
}
