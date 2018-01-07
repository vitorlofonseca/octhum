<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblSynapse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_synapse', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_source_neuron')->unsigned();
            $table->foreign('id_source_neuron')->references('id')->on('tbl_neuron');
            $table->integer('id_destination_neuron')->unsigned();
            $table->foreign('id_destination_neuron')->references('id')->on('tbl_neuron');
            $table->float('weight');

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
        Schema::drop('tbl_synapse');
    }
}
