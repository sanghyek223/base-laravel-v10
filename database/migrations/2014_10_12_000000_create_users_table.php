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
        Schema::create('users', function (Blueprint $table) {
            $table->id('sid');
            $table->string('uid')->index()->comment('ID');
            $table->string('password')->comment('비밀번호');
            $table->string('level', 10)->comment('회원등급');
            $table->string('name_kr')->index()->comment('이름');
            $table->string('mobile')->comment('휴대폰');
            $table->string('email')->comment('이메일');
            $table->string('license_number')->nullable()->comment('면허번호');
            $table->timestamps();
            $table->timestamp('password_at')->nullable()->comment('비밀번호 변경 시간');
            $table->softDeletes()->comment('삭제일');

            $table->comment('회원 테이블');
        });

        \App\Models\User::create([
            'uid' => 'webmaster',
            'password' => \Illuminate\Support\Facades\Hash::make('123123'),
            'level' => 'M',
            'email' => 'test@test.com',
            'name_kr' => '테스트',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
