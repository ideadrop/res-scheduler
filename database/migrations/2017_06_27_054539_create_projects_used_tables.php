<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectsUsedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        
        Schema::create('projects_used', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('project_id')->unsigned();
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
            
			$table->foreign('project_id')->references('id')->on('projects')
						->onDelete('cascade')
						->onUpdate('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
        Schema::table('projects_used', function(Blueprint $table) {
			$table->dropForeign('projects_used_user_id_foreign');
		});

		Schema::table('projects_used', function(Blueprint $table) {
			$table->dropForeign('projects_used_project_id_foreign');
		});

        Schema::drop('projects_used');
        
    }
}
