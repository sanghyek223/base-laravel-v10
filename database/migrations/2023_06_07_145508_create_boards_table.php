<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->id('sid');
            $table->string('code')->index()->comment('게시판 구분 값');
            $table->bigInteger('u_sid')->index()->unsigned()->default(0)->comment('작성자');
            $table->string('password')->nullable()->comment('비밀글일경우 비밀번호');
            $table->string('writer')->nullable()->comment('작성자');
            $table->string('email')->nullable()->comment('이메일');
            $table->string('gubun')->nullable()->comment('게시판 분류');
            $table->string('category')->nullable()->comment('게시판 카테고리');
            $table->string('subject')->comment('제목');
            $table->longText('contents')->nullable()->comment('내용');
            $table->string('link_url')->nullable()->comment('링크 url');
            $table->string('notice_sDate')->nullable()->comment('공지 시작일');
            $table->string('notice_eDate')->nullable()->comment('공지 종료일');
            $table->enum('date_type', ['D', 'L'])->default('D')->comment('기간 타입 D: 하루, L: 기간');
            $table->string('event_sDate')->nullable()->comment('행사 시작일');
            $table->string('event_eDate')->nullable()->comment('행사 종료일');
            $table->string('place')->nullable()->comment('장소');
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
            $table->enum('popup', ['Y', 'N'])->default('N')->comment('팝업 설정');
            $table->enum('notice', ['Y', 'N'])->default('N')->comment('공지 설정');
            $table->enum('main', ['Y', 'N'])->default('Y')->comment('메인 설정');
            $table->enum('hide', ['Y', 'N'])->default('N')->comment('노출 설정');
            $table->enum('secret', ['Y', 'N'])->default('N')->comment('비밀글 설정');
            $table->integer('ref')->unsigned()->default(0)->comment('조회수');
            $table->timestamps();
            $table->comment('게시판 테이블');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boards');
    }
}
