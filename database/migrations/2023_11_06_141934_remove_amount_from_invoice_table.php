<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveAmountFromInvoiceTable extends Migration
{
    public function up(): void
    {
        Schema::table('invoice', function (Blueprint $table) {

        });
    }

    public function down(): void
    {
        Schema::table('invoice', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable();
        });
    }
}
