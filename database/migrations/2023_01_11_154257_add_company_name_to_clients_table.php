<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyNameToClientsTable extends Migration
{
	public function up()
	{
		Schema::table('clients', function (Blueprint $table) {
			$table->string('company_name')->nullable();
		});
	}

	public function down()
	{
		Schema::table('clients', function (Blueprint $table) {
			$table->dropColumn('company_name');
		});
	}
}