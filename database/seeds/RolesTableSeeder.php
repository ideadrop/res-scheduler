<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
    		[
        		'name' => 'admin',
        		'display_name' => 'Administrator',
        		'description' => 'Super Admin'
        	],
        	[
        		'name' => 'project_manager',
        		'display_name' => 'Project Manager',
        		'description' => 'Project Manager'
        	],
        	[
        		'name' => 'team_lead',
        		'display_name' => 'Team Lead',
        		'description' => 'Team Lead'
        	],
        	[
        		'name' => 'developer',
        		'display_name' => 'Developer',
        		'description' => 'Developer'
        	]
    	];

    	foreach ($roles as $key => $value) {
        	Role::create($value);
        }
    }
}
