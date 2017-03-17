<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblHistoryTroubleProcesslist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_history_trouble_processlist', function (Blueprint $table) {
            $table->increments('id');
            $table->string('host');
            $table->string('user');
            $table->integer('process_id');
            $table->text('query');
            $table->integer('time');
            $table->tinyInteger('is_kill');
            $table->integer('kill_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tbl_history_trouble_processlist');
    }
}
