<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUploadToProposalsTable extends Migration
{
	public function up()
	{
		Schema::table('proposals', function (Blueprint $table) {
			$table->longText('upload')->nullable();
		});
	}

	public function down()
	{
		Schema::table('proposals', function (Blueprint $table) {
			$table->dropColumn('upload');
		});
	}
}