<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('board_reply_files', function (Blueprint $table) {
            $table->id('sid');
            $table->unsignedBigInteger('br_sid')->index()->comment('board.sid');
            $table->unsignedBigInteger('u_sid')->index()->comment('파일 업로드 실행자');
            $table->string('realfile')->comment('파일경로');
            $table->string('filename')->comment('원본 파일명');
            $table->unsignedInteger('download')->default(0)->comment('다운로드 수');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->comment('게시판 답글 첨부파일');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_reply_files');
    }
};
