<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblLayer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_layer', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('b_output');
            $table->integer('id_neural_network')->unsigned();
            $table->foreign('id_neural_network')->references('id')->on('tbl_neural_network');
            $table->boolean('b_input');
            $table->boolean('b_hidden');
            $table->integer('qtd_neurons');

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
        Schema::drop('tbl_layer');
    }
}
