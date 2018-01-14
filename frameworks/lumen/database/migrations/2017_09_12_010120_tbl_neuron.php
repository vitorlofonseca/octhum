<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblNeuron extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_neuron', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_layer')->unsigned();
            $table->foreign('id_layer')->references('id')->on('tbl_layer');
            $table->float('output');
            $table->boolean('b_bia');

            $table->timestamps();
            $table->integer('id_resp_inc')->unsigned();
            $table->integer('id_resp_alt')->unsigned()->nullable();
            $table->foreign('id_resp_inc')->references('id')->on('tbl_user');
            $table->foreign('id_resp_alt')->references('id')->on('tbl_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_neuron');
    }
}
