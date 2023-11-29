<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::raw("ALTER TABLE `users` CHANGE `discount_comission` `discount_comission` INT(11) NULL DEFAULT '0';");
        });
    }
};
