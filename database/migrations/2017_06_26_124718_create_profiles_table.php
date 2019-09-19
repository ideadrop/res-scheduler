<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfilesTable extends Migration {

	public function up()
	{
		Schema::create('profiles', function(Blueprint $table) {
			$table->increments('id');
			$table->string('first_name', 100);
			$table->string('last_name', 100)->nullable();
			$table->string('company', 100)->nullable();
			$table->string('designation', 100)->nullable();
			$table->text('address_line1');
			$table->text('address_line2')->nullable();
			$table->string('phone', 20)->nullable();
			$table->string('city', 100)->nullable();
			$table->string('state', 100)->nullable();
			$table->string('country', 100)->nullable();
			$table->string('zipcode', 10)->nullable();
			$table->timestamps();
			$table->integer('user_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('profiles');
	}
}