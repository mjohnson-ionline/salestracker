<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->float('dollar_amount');
            $table->foreignId('client_id')->constrained();
	        $table->string('status')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('canva_link')->nullable();;
            $table->timestamp('signed_at')->nullable();;
            $table->longText('notes')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proposals');
    }
}
