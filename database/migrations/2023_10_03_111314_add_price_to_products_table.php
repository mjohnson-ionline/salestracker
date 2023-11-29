<?php
	
	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;
	
	class AddPriceToProductsTable extends Migration
	{
		public function up (): void
		{
			Schema::table ('products', function (Blueprint $table) {
				$table->decimal ('price', 8, 2)->after ('percentage')->nullable ();
			});
		}
		
		public function down (): void
		{
			Schema::table ('products', function (Blueprint $table) {
				$table->dropColumn ('price');
			});
		}
	}
