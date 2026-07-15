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
        Schema::create('board_comments', function (Blueprint $table) {
            $table->id('sid');
            $table->unsignedBigInteger('u_sid')->index()->comment('user_binfo.sid, 비회원: 0');
            $table->unsignedBigInteger('b_sid')->index()->comment('boards.sid');
            $table->unsignedBigInteger('depth1')->index()->comment('board_comments.sid 1차 상위댓글');
            $table->unsignedBigInteger('depth2')->index()->comment('board_comments.sid 2차상위 댓글');
            $table->unsignedInteger('thread')->default(0)->comment('들여쓰기');
            $table->string('writer')->comment('작성자');
            $table->longText('comment')->comment('댓글 내용');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes()->comment('삭제일');
            $table->comment('게시판 댓글');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_comments');
    }
};
