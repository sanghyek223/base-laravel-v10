<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailAddressDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_address_details', function (Blueprint $table) {
            $table->id('sid');
            $table->bigInteger('ma_sid')->index()->unsigned()->comment('mail_addresses.sid');
            $table->string('name')->comment('이름');
            $table->string('email')->comment('email');
            $table->string('mobile')->comment('휴대폰번호');
            $table->timestamps();
            $table->comment('메일 주소록 상세');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_address_details');
    }
}
