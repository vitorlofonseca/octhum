<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblIntelligenceCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_intelligence_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category');
        });

        DB::table('tbl_intelligence_category')->insert(
            array(
                'category' => 'Neural Network'
            )
        );

        DB::table('tbl_intelligence_category')->insert(
            array(
                'category' => 'Specialist System'
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_intelligence_category');
    }
}