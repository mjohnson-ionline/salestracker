<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropModelHasPermissionsTable extends Migration
{
    public function up(): void
    {
        Schema::table('model_has_permissions', function (Blueprint $table) {
            // disable foreign key constrainst
            Schema::disableForeignKeyConstraints();
            Schema::dropIfExists('model_has_permissions');
            Schema::dropIfExists('model_has_roles');
            Schema::dropIfExists('roles');
            Schema::dropIfExists('role_has_permissions');
            Schema::dropIfExists('permissions');
            // enable foreign key constrainst
            Schema::enableForeignKeyConstraints();
        });
    }

    public function down(): void
    {
        Schema::table('model_has_permissions', function (Blueprint $table) {
            //
        });
    }
}
