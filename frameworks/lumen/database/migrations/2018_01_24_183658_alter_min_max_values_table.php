<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMinMaxValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('tbl_min_max_values');

        Schema::create('tbl_min_max_values', function (Blueprint $table) {
            $table->increments('id');
            $table->string('min_or_max', 50);
            $table->float('value');
            $table->integer('id_variable')->unsigned();
            $table->foreign('id_variable')->references('id')->on('tbl_mlp_variable');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
