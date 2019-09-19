<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectsTable extends Migration {

	public function up()
	{
		Schema::create('projects', function(Blueprint $table) {
			$table->increments('id');
			$table->string('project_code', 250);
			$table->string('name', 255);
			$table->timestamp('start_date');
			$table->timestamp('end_date');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('projects');
	}
}