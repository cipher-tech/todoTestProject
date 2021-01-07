<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTodoListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todo_lists', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->integer('user_id');
            $table->string('label')->nullable();
            $table->string('slug')->nullable();
            $table->string('comment')->nullable();
            $table->enum("status", ["completed", "ongoing", "not started"])->default("not started");
            $table->enum("priority", ["normal", "important", "very important"])->default("normal");
            $table->dateTime('estimated_start_date')->nullable();
            $table->dateTime('estimated_end_date')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->boolean('reminder')->default(false);
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
        Schema::dropIfExists('todo_lists');
    }
}
