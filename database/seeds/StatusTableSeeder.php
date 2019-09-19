<?php

use Illuminate\Database\Seeder;
use App\Status;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = [
    		['name' => 'New'],
    		['name' => 'In-Progress'],
    		['name' => 'Resolved'],
    		['name' => 'Closed']
    	];

    	foreach ($status as $key => $value) {
        	Status::create($value);
        }
    }
}
