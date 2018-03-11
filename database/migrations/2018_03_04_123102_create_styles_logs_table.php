<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStylesLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('styles_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->string('email');
            $table->tinyInteger('newsletter');
            $table->integer('style_take');
            $table->integer('style_avoid');
            $table->string('ip_address');
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
        Schema::dropIfExists('styles_logs');
    }
}
