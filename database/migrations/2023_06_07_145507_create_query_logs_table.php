<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('query_logs', function (Blueprint $table) {
            $table->id('sid');
            $table->bigInteger('u_sid')->index()->unsigned()->comment('users.sid (로그인 정보없을시 0)');
            $table->string('subject')->index()->comment('동작 설명');
            $table->longText('query')->comment('쿼리');
            $table->string('ip')->comment('ip');
            $table->timestamps();
            $table->comment('쿼리 로그');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('query_logs');
    }
}
