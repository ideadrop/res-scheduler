<?php

use Illuminate\Database\Seeder;
use App\Usertype;

class UsertypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_types = [
    		['name' => 'CEO'],
    		['name' => 'CTO'],
    		['name' => 'CFO'],
    		['name' => 'Project Manager'],
    		['name' => 'Team Lead'],
    		['name' => 'UI/UX Designer'],
    		['name' => 'Sr. Software Engineer'],
    		['name' => 'Software Engineer'],
    		['name' => 'Jr. Software Engineer'],
    		['name' => 'Sr. Marketing Manager'],
    		['name' => 'Sales Manager'],
    		['name' => 'Sales Manager Trainee'],
    		['name' => 'Sr. Quality Analyst'],
    		['name' => 'Quality Analyst'],
    		['name' => 'Jr. Quality Analyst'],
    		['name' => 'IT Trainee'],
    		['name' => 'Financial Analyst'],
    		['name' => 'Content Writer'],
    		['name' => 'Programmer Analyst'],
    		['name' => 'Human Resource Manager']
    	];

    	foreach ($user_types as $key => $value) {
        	Usertype::create($value);
        }
    }
}
