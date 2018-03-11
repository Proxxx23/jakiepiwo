<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_user')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('e_mail', 100)->nullable();
            $table->tinyInteger('newsletter');
            $table->text('answers');
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
        Schema::dropIfExists('user_answers');
    }
}
