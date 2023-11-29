<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToResellerTable extends Migration
{
    public function up(): void
    {
        Schema::table('resellers', function (Blueprint $table) {
            $table->string('company_name')->nullable();
            $table->string('abn')->nullable();
            $table->string('status')->nullable();
            $table->text('additional_notes')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('resellers', function (Blueprint $table) {
            //
        });
    }
}
