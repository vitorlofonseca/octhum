<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblClassification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_classification', function (Blueprint $table) {
            $table->increments('id');
            $table->string('classification');
            $table->integer('id_neural_network')->unsigned();
            $table->foreign('id_neural_network')->references('id')->on('tbl_neural_network');

            $table->integer('id_neuron')->unsigned();
            $table->foreign('id_neuron')->references('id')->on('tbl_neuron');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_classification');
    }
}