<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('profiles', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		// Schema::table('profiles', function(Blueprint $table) {
		// 	$table->foreign('designation')->references('id')->on('user_types')
		// 				->onDelete('cascade')
		// 				->onUpdate('cascade');
		// });
		Schema::table('skills_used', function(Blueprint $table) {
			$table->foreign('skill_id')->references('id')->on('skills')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('notifications', function(Blueprint $table) {
			$table->foreign('user_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('notifications', function(Blueprint $table) {
			$table->foreign('author_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('tags', function(Blueprint $table) {
			$table->foreign('author_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('tasks', function(Blueprint $table) {
			$table->foreign('project_id')->references('id')->on('projects')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('tasks', function(Blueprint $table) {
			$table->foreign('assignee_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('tasks', function(Blueprint $table) {
			$table->foreign('assigner_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('tasks', function(Blueprint $table) {
			$table->foreign('status_id')->references('id')->on('statuses')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('allocations', function(Blueprint $table) {
			$table->foreign('project_id')->references('id')->on('projects')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('allocations', function(Blueprint $table) {
			$table->foreign('assignee_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('allocations', function(Blueprint $table) {
			$table->foreign('assigner_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('day_allocations', function(Blueprint $table) {
			$table->foreign('project_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('day_allocations', function(Blueprint $table) {
			$table->foreign('assignee_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('day_allocations', function(Blueprint $table) {
			$table->foreign('assigner_id')->references('id')->on('users')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
	}

	public function down()
	{
		Schema::table('skills_used', function(Blueprint $table) {
			$table->dropForeign('skills_used_skill_id_foreign');
		});
		Schema::table('notifications', function(Blueprint $table) {
			$table->dropForeign('notifications_user_id_foreign');
		});
		Schema::table('notifications', function(Blueprint $table) {
			$table->dropForeign('notifications_author_id_foreign');
		});
		Schema::table('tags', function(Blueprint $table) {
			$table->dropForeign('tags_author_id_foreign');
		});
		Schema::table('tasks', function(Blueprint $table) {
			$table->dropForeign('tasks_project_id_foreign');
		});
		Schema::table('tasks', function(Blueprint $table) {
			$table->dropForeign('tasks_assignee_id_foreign');
		});
		Schema::table('tasks', function(Blueprint $table) {
			$table->dropForeign('tasks_assigner_id_foreign');
		});
		Schema::table('tasks', function(Blueprint $table) {
			$table->dropForeign('tasks_status_id_foreign');
		});
		Schema::table('allocations', function(Blueprint $table) {
			$table->dropForeign('allocations_project_id_foreign');
		});
		Schema::table('allocations', function(Blueprint $table) {
			$table->dropForeign('allocations_assignee_id_foreign');
		});
		Schema::table('allocations', function(Blueprint $table) {
			$table->dropForeign('allocations_assigner_id_foreign');
		});
		Schema::table('day_allocations', function(Blueprint $table) {
			$table->dropForeign('day_allocations_project_id_foreign');
		});
		Schema::table('day_allocations', function(Blueprint $table) {
			$table->dropForeign('day_allocations_assignee_id_foreign');
		});
		Schema::table('day_allocations', function(Blueprint $table) {
			$table->dropForeign('day_allocations_assigner_id_foreign');
		});
	}
}