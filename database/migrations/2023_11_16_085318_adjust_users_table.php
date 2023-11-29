<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::raw("ALTER TABLE `users` DROP COLUMN `office365_login`;");
            DB::raw("ALTER TABLE `users` DROP COLUMN `office365_password`;");
        });
    }

    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {
            //
        });
    }
};
