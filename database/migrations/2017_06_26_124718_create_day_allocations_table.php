<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDayAllocationsTable extends Migration {

	public function up()
	{
		Schema::create('day_allocations', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('project_id')->unsigned();
			$table->timestamp('date');
			$table->integer('assignee_id')->unsigned();
			$table->integer('assigner_id')->unsigned();
			$table->enum('allocation_type', array('percentage', 'hours', 'total'));
			$table->integer('allocation_value');
			$table->enum('repeat_type', array('none', 'daily', 'weekly', 'monthly'));
			$table->string('repeat_value')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('day_allocations');
	}
}