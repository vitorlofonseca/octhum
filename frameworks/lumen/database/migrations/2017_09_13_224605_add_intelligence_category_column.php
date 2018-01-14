<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIntelligenceCategoryColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_intelligence', function($table) {
            $table->integer('id_category')->unsigned();
            $table->foreign('id_category')->references('id')->on('tbl_intelligence_category');
        });
    }

    public function down()
    {
        Schema::table('tbl_intelligence', function($table) {
            $table->dropColumn('id_category');
        });
    }
}
