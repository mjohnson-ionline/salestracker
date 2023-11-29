<?php
	
	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;
	
	class AddPipedriveIdToProductsTable extends Migration
	{
		public function up (): void
		{
			Schema::table ('products', function (Blueprint $table) {
				$table->integer ('pipedrive_id')->after ('xero_code')->nullable ();
			});
		}
		
		public function down (): void
		{
			Schema::table ('products', function (Blueprint $table) {
				$table->dropColumn ('pipedrive_id');
			});
		}
	}
