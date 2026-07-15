<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_counters', function (Blueprint $table) {
            $table->id('sid');
            $table->bigInteger('b_sid')->index()->unsigned()->default(0)->comment('boards.sid');
            $table->string('ip')->comment('접속 ip');
            $table->timestamps();
            $table->comment('게시판 카운터');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_counters');
    }
}
