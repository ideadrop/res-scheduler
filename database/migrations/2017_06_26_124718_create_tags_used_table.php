<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTagsUsedTable extends Migration {

	public function up()
	{
		Schema::create('tags_used', function(Blueprint $table) {
			$table->increments('id');
			$table->enum('tag_type', array('user', 'project'));
			$table->integer('tag_id');
			$table->integer('target_id')->unsigned();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('tags_used');
	}
}