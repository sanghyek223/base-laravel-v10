<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_files', function (Blueprint $table) {
            $table->id('sid');
            $table->bigInteger('ml_sid')->index()->unsigned()->comment('mail_lists.sid');
            $table->bigInteger('u_sid')->index()->unsigned()->default(0)->comment('파일 업로드 실행자');
            $table->string('realfile')->comment('파일경로');
            $table->string('filename')->comment('원본 파일명');
            $table->integer('download')->unsigned()->default(0)->comment('다운로드 수');
            $table->timestamps();
            $table->comment('메일 첨부파일');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_files');
    }
}
