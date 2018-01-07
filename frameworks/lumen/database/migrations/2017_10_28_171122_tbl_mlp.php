<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblMlp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::dropIfExists('tbl_neural_network');

        Schema::create('tbl_mlp', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_intelligence')->unsigned();
            $table->foreign('id_intelligence')->references('id')->on('tbl_intelligence');
            $table->string('conf_file_name', 200);

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
        //
    }
}
