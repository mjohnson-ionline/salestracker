<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToProposalsTable extends Migration
{
	public function up()
	{
		Schema::table('proposals', function (Blueprint $table) {
			$table->longText('image')->nullable();
		});
	}

	public function down()
	{
		Schema::table('proposals', function (Blueprint $table) {
			$table->dropColumn('image');
		});
	}
}