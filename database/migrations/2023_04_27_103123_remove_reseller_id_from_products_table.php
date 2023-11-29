<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveResellerIdFromProductsTable extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
        $table->dropForeign('products_reseller_id_foreign');
            $table->dropColumn('reseller_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
