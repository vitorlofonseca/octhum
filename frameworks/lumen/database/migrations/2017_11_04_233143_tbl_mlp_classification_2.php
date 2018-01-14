<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblMlpClassification2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::dropIfExists('tbl_mlp_classification');

        Schema::create('tbl_mlp_classification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_mlp')->unsigned();
            $table->foreign('id_mlp')->references('id')->on('tbl_mlp');
            $table->string('name', 200);
            $table->string('output_number', 200);
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
