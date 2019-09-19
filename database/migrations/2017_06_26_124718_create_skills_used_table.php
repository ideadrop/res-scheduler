<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSkillsUsedTable extends Migration {

	public function up()
	{
		Schema::create('skills_used', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('skill_id')->unsigned();
			$table->timestamps();
			$table->enum('item_type', array('user', 'project'));
			$table->integer('item_id');
		});
	}

	public function down()
	{
		Schema::drop('skills_used');
	}
}