<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblIntelligenceLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_intelligence_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_name');
            $table->integer('id_intelligence')->unsigned();
            $table->integer('id_type_log')->unsigned();

            $table->foreign('id_intelligence')->references('id')->on('tbl_intelligence');
            $table->foreign('id_type_log')->references('id')->on('tbl_type_log');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_intelligence_log');
    }
}
