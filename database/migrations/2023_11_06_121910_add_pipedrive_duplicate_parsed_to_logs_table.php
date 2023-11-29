<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPipedriveDuplicateParsedToLogsTable extends Migration
{
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->boolean('pipedrive_duplicate_parsed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn('pipedrive_duplicate_parsed');
        });
    }
}
