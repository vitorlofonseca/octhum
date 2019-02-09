<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblMinMaxValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('tbl_min_max_values', function (Blueprint $table) {
            $table->increments('id');
            $table->string('min_or_max', 50);
            $table->float('value');
            $table->integer('id_mlp')->unsigned();
            $table->foreign('id_mlp')->references('id')->on('tbl_mlp');
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
