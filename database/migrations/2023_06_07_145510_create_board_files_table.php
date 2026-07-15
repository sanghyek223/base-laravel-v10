<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_files', function (Blueprint $table) {
            $table->id('sid');
            $table->bigInteger('b_sid')->index()->unsigned()->comment('board.sid');
            $table->bigInteger('u_sid')->index()->unsigned()->default(0)->comment('파일 업로드 실행자');
            $table->string('realfile')->comment('파일경로');
            $table->string('filename')->comment('원본 파일명');
            $table->integer('download')->unsigned()->default(0)->comment('다운로드 수');
            $table->timestamps();
            $table->comment('게시판 첨부파일');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('board_files');
    }
}
