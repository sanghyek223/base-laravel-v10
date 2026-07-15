<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardPopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board_popups', function (Blueprint $table) {
            $table->id('sid');
            $table->bigInteger('b_sid')->index()->unsigned()->comment('board.sid');
            $table->bigInteger('u_sid')->index()->unsigned()->default(0)->comment('작성자');
            $table->char('popup_skin', 1)->default('1')->comment('팝업 스킨');
            $table->enum('popup_select', ['1', '2'])->default('1')->comment('팝업 내용 선택값');
            $table->longText('popup_contents')->comment('팝업 내용');
            $table->smallInteger('width')->nullable()->unsigned()->default(500)->comment('width');
            $table->smallInteger('height')->nullable()->unsigned()->default(600)->comment('height');
            $table->smallInteger('position_x')->nullable()->unsigned()->default(0)->comment('x 좌표 px');
            $table->smallInteger('position_y')->nullable()->unsigned()->default(0)->comment('y 좌표 px');
            $table->enum('popup_detail', ['Y', 'N'])->default('N')->comment('팝업 상세보기 사용 유무');
            $table->string('popup_link')->nullable()->comment('팝업 상세보기 링크');
            $table->string('popup_sDate')->nullable()->comment('팝업 시작일');
            $table->string('popup_eDate')->nullable()->comment('팝업 종료일');
            $table->timestamps();
            $table->comment('게시판 팝업 테이블');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boardã_popups');
    }
}
