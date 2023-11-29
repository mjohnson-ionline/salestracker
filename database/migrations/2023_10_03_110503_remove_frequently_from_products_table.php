<?php
	
	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;
	
	class RemoveFrequentlyFromProductsTable extends Migration
	{
		public function up (): void
		{
			Schema::table ('products', function (Blueprint $table) {
				$table->dropColumn ('frequently');
			});
		}
		
		public function down (): void
		{
			Schema::table ('products', function (Blueprint $table) {
				$table->boolean ('frequently')->default (false);
			});
		}
	}
