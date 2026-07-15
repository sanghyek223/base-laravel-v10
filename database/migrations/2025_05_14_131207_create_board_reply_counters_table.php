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
        Schema::create('board_reply_counters', function (Blueprint $table) {
            $table->id('sid');
            $table->unsignedBigInteger('br_sid')->index()->comment('board_replies.sid');
            $table->string('ip')->comment('접속 ip');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->comment('게시판 답글 카운터');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_reply_counters');
    }
};
