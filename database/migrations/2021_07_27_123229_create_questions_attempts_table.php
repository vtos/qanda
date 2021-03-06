<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained();
            $table->string('user_answer');
            $table->string('status');
            $table->timestamps();

            $table->unique('question_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions_attempts');
    }
}
