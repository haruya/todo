<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodoTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// projectsテーブルの作成
		Schema::create('projects', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name', 64);
			$table->integer('seq');
			$table->enum('status', ['notyet', 'done'])->default('notyet');
			$table->timestamps();
		});

		// tasksテーブルの作成
		Schema::create('tasks', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('project_id');
			$table->string('title', 64);
			$table->text('content')->nullable();
			$table->text('remarks')->nullable();
			$table->integer('seq');
			$table->enum('status', ['before_work', 'working', 'after_work'])->default('before_work');
			$table->integer('priority');
			$table->string('worker', 32)->nullable();
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
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
		Schema::drop('projects');
		Schema::drop('tasks');
	}

}
