<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDescriptionsTable extends Migration {

	public function up()
	{
		Schema::create('descriptions', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->integer('item_id');
			$table->string('item_type', 100);
            $table->text('value');
		});
	}

	public function down()
	{
		Schema::drop('descriptions');
	}
}