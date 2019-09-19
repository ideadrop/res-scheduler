<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionTableSeeder::class);
    	$this->call(RolesTableSeeder::class);
    	$this->call(PermissionRoleTableSeeder::class);
    	$this->call(SkillTableSeeder::class);
    	$this->call(StatusTableSeeder::class);
    	$this->call(UsertypeTableSeeder::class);
    }
}
