<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password', 500);
            $table->string('username');

            $table->timestamps();
            $table->integer('id_resp_inc')->unsigned();
            $table->integer('id_resp_alt')->unsigned()->nullable();
        });

        DB::table('tbl_user')->insert(
            array(
                'name' => 'test',
                'email' => 'admin@admin.com',
                'password' => 'test',
                'username' => 'test'
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
        Schema::drop('tbl_user');
    }
}
