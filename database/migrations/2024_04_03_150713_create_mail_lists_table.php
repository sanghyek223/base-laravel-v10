<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_lists', function (Blueprint $table) {
            $table->id('sid');
            $table->string('template')->comment('메일 템플릿');
            $table->string('sender_name')->comment('발송자');
            $table->string('sender_email')->comment('발송 이메일');
            $table->string('subject')->comment('메일제목');
            $table->mediumText('contents')->comment('메일 내용');
            $table->tinyInteger('send_type')->unsigned()->comment('메일발송 타입');
            $table->tinyInteger('use_btn')->unsigned()->comment('버튼 사용');
            $table->string('link_url')->nullable()->comment('버튼 사용시 링크');
            $table->mediumText('level')->nullable()->comment('회원등급별 발송일경우 users.level');
            $table->bigInteger('ma_sid')->unsigned()->nullable()->comment('주소록 발송일경우 mail_addresses.sid');
            $table->string('test_email')->nullable()->comment('테스트 발송일경우 테스트 수신 이메일');
            $table->integer('thread')->unsigned()->default(0)->comment('발송횟수');
            $table->timestamp('send_date')->nullable()->comment('메일 최근 발송시간');
            $table->integer('readyCnt')->unsigned()->default(0)->comment('발송대기 메일');
            $table->integer('failCnt')->unsigned()->default(0)->comment('발송 실패 메일');
            $table->integer('sucCnt')->unsigned()->default(0)->comment('발송 성공 메일');
            $table->timestamps();
            $table->softDeletes()->comment('삭제일');
            $table->comment('메일 테이블');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_lists');
    }
}
