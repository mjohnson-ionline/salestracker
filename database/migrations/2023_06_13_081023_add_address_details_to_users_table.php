<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_suburb')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_postcode')->nullable();
            $table->string('address_country')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address_line_1',
                'address_line_2',
                'address_suburb',
                'address_state',
                'address_postcode',
                'address_country',
            ]);
        });
    }
};
