<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenToProposalTable extends Migration
{
	public function up()
	{
		Schema::table('proposals', function (Blueprint $table) {
			$table->string('token')->nullable();
		});
	}

	public function down()
	{
		Schema::table('proposals', function (Blueprint $table) {
			$table->dropColumn('token');
		});
	}
}