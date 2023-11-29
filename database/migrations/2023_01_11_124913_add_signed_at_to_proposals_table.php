<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSignedAtToProposalsTable extends Migration
{
	public function up()
	{
		Schema::table('proposals', function (Blueprint $table) {
			$table->ipAddress('signed_ip')->nullable();
		});
	}

	public function down()
	{
		Schema::table('proposals', function (Blueprint $table) {
			$table->dropColumn('signed_ip');
		});
	}
}