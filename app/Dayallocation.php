<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dayallocation extends Model 
{

    protected $table = 'day_allocations';
    public $timestamps = true;
    protected $fillable = array('project_id', 'date', 'assignee_id', 'assigner_id', 'allocation_type', 'allocation_value', 'repeat_type', 'repeat_value');    
}