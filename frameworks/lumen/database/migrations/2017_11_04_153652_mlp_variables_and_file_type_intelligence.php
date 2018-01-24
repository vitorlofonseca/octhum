<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MlpVariablesAndFileTypeIntelligence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_mlp_variables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_mlp')->unsigned();
            $table->foreign('id_mlp')->references('id')->on('tbl_mlp');
            $table->string('name', 200);
        });

        Schema::create('tbl_intelligence_data_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 200);
        });

        Schema::table('tbl_intelligence', function (Blueprint $table) {
            $table->integer('id_data_type')->unsigned();
            $table->foreign('id_data_type')->references('id')->on('tbl_intelligence_data_type');
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
