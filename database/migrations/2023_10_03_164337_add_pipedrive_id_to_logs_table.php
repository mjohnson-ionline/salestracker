<?php
	
	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;
	
	class AddPipedriveIdToLogsTable extends Migration
	{
		public function up (): void
		{
			Schema::table ('logs', function (Blueprint $table) {
				$table->integer ('webhook_id')->after('id')->nullable ();
			});
		}
		
		public function down (): void
		{
			Schema::table ('logs', function (Blueprint $table) {
				$table->dropColumn ('webhook_id');
			});
		}
	}
