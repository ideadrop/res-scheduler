<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = [
        	[
        		'name' => 'role-list',
        		'display_name' => 'Display Role Listing',
        		'description' => 'See only Listing Of Role'
        	],
        	[
        		'name' => 'role-create',
        		'display_name' => 'Create Role',
        		'description' => 'Create New Role'
        	],
        	[
        		'name' => 'role-edit',
        		'display_name' => 'Edit Role',
        		'description' => 'Edit Role'
        	],
        	[
        		'name' => 'role-delete',
        		'display_name' => 'Delete Role',
        		'description' => 'Delete Role'
        	],
        	[
        		'name' => 'project-create',
        		'display_name' => 'Create Project',
        		'description' => 'Create New Project'
        	],
        	[
        		'name' => 'project-edit',
        		'display_name' => 'Edit Project',
        		'description' => 'Edit Project'
        	],
        	[
        		'name' => 'project-delete',
        		'display_name' => 'Delete Project',
        		'description' => 'Delete Project'
        	],
			[
				'name' => 'project-list-allocate',
				'display_name' => 'List/Allocate project',
				'description' => 'See listing of project and create/edit/remove its allocations'
			],
        	[
        		'name' => 'resource-create',
        		'display_name' => 'Create Resource',
        		'description' => 'Create New Resource'
        	],
        	[
        		'name' => 'resource-edit',
        		'display_name' => 'Edit Resource',
        		'description' => 'Edit Resource'
        	],
        	[
        		'name' => 'resource-disable-enable',
        		'display_name' => 'Disable Resource',
        		'description' => 'Disable Resource'
        	],
			[
				'name' => 'resource-list-allocate',
				'display_name' => 'List/Allocate Resource',
				'description' => 'See listing of resources and create/edit/remove its allocations'
			],
        	[
        		'name' => 'view-my-project-calender',
        		'display_name' => 'Display Of Own Resource Show Page',
        		'description' => 'View Our Own Resource Allocation Calender Page'
        	],
			[
				'name' => 'view-report',
				'display_name' => 'View and export reports',
				'description' => 'View and export project, user, skill, tag based reports'
			],
			[
				'name' => 'view-dashboard',
				'display_name' => 'View Dashboard',
				'description' => 'View Dashboard items'
			],
			[
				'name' => 'resource-release-mail',
				'display_name' => 'Get Resource Release Mail',
				'description' => 'Notification mail [X] days before a user will be released from a project'
			],
			[
				'name' => 'resource-engage-report-mail',
				'display_name' => 'Get Resource Engagement Report Mail',
				'description' => 'Weekly report of engaged resources and free resources'
			],
			[
				'name' => 'skill-resource-report-mail',
				'display_name' => 'Get Skilled Free Resource Report Mail',
				'description' => 'Weekly report of skill sets which have free resources'
			]
        ];

        foreach ($permission as $key => $value) {
        	Permission::create($value);
        }
    }
}
