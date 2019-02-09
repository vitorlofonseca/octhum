<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangingNameTypeLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename("tbl_type_log", "tbl_log_type");

        Schema::table('tbl_intelligence_log', function(Blueprint $table)
        {
            $table->renameColumn('id_type_log', 'id_log_type');

        });

        DB::table('tbl_log_type')->insert(
            array(
                'type' => 'Creation'
            )
        );

        DB::table('tbl_log_type')->insert(
            array(
                'type' => 'Use'
            )
        );

        DB::table('tbl_log_type')->insert(
            array(
                'type' => 'Modification'
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
        //
    }
}
