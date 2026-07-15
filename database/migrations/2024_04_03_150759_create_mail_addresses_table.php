<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_addresses', function (Blueprint $table) {
            $table->id('sid');
            $table->string('title')->nullable()->comment('주소록 명');
            $table->timestamps();
            $table->comment('메일 주소록');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_addresses');
    }
}
