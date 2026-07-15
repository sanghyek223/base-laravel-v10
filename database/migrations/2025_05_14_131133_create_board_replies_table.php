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
        Schema::create('board_replies', function (Blueprint $table) {
            $table->id('sid');
            $table->unsignedBigInteger('b_sid')->index()->comment('boards.sid');
            $table->unsignedBigInteger('u_sid')->index()->comment('user_binfo.sid');
            $table->string('writer')->nullable()->comment('작성자');
            $table->string('email')->nullable()->comment('작성자 이메일');
            $table->string('subject')->comment('제목');
            $table->longText('contents')->comment('내용');
            $table->string('link_url')->nullable()->comment('링크 url');
            $table->unsignedInteger('ref')->default(0)->comment('조회수');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->comment('게시판 답글');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_replies');
    }
};
