<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('xero_syncs', function (Blueprint $table) {
            $table->id();
            $table->string('contact_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email_address')->nullable();
            $table->string('primary_phone')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_suburb')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_postcode')->nullable();
            $table->string('address_country')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('xero_syncs');
    }
};
