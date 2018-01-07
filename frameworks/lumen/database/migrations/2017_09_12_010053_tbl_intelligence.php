<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblIntelligence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_intelligence', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description', 500);

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
        Schema::drop('tbl_intelligence');
    }
}
