<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referers', function (Blueprint $table) {
            $table->id('sid');
            $table->string('ip');
            $table->string('u_sid')->index()->nullable()->comment('users.sid (회원일경우)');
            $table->string('referer')->nullable()->nullable('접속 경로');
            $table->string('browser')->index()->comment('검색엔진');
            $table->string('platform')->index()->comment('OS');
            $table->string('keyword')->nullable()->comment('검색어');
            $table->string('lang')->index()->default('ko')->comment('사이트 구분');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referers');
    }
}
