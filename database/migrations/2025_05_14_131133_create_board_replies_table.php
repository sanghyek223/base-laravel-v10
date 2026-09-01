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
            $table->string('realfile1')->nullable()->comment('단일 파일1 파일경로');
            $table->string('filename1')->nullable()->comment('단일 파일1 파일명');
            $table->integer('file1_download')->unsigned()->default(0)->comment('단일 파일1 다운로드수');
            $table->string('realfile2')->nullable()->comment('단일 파일2 파일경로');
            $table->string('filename2')->nullable()->comment('단일 파일2 파일명');
            $table->integer('file2_download')->unsigned()->default(0)->comment('단일 파일2 다운로드수');
            $table->string('realfile3')->nullable()->comment('단일 파일3 파일경로');
            $table->string('filename3')->nullable()->comment('단일 파일3 파일명');
            $table->integer('file3_download')->unsigned()->default(0)->comment('단일 파일3 다운로드수');
            $table->string('realfile4')->nullable()->comment('단일 파일4 파일경로');
            $table->string('filename4')->nullable()->comment('단일 파일4 파일명');
            $table->integer('file4_download')->unsigned()->default(0)->comment('단일 파일4 다운로드수');
            $table->string('realfile5')->nullable()->comment('단일 파일5 파일경로');
            $table->string('filename5')->nullable()->comment('단일 파일5 파일명');
            $table->integer('file5_download')->unsigned()->default(0)->comment('단일 파일5 다운로드수');
            $table->string('thumbnail_realfile')->nullable()->comment('썸네일 파일경로');
            $table->string('thumbnail_filename')->nullable()->comment('썸네일 파일명');
            $table->integer('thumbnail_download')->unsigned()->default(0)->comment('썸네일 다운로드수');
            $table->unsignedInteger('ref')->default(0)->comment('조회수');

            $table->timestamps();
            $table->softDeletes()->comment('삭제일');

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
