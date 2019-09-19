<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAllocationsTable extends Migration {

	public function up()
	{
		Schema::create('allocations', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('project_id')->unsigned();
			$table->timestamp('start_date');
			$table->timestamp('end_date');
			$table->integer('assignee_id')->unsigned();
			$table->integer('assigner_id')->unsigned();
			$table->enum('allocation_type', array('percentage', 'hours', 'total'));
			$table->integer('allocation_value');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('allocations');
	}
}