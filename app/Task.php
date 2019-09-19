<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model 
{

    protected $table = 'tasks';
    public $timestamps = true;
    protected $fillable = array('project_id', 'name', 'description', 'assignee_id', 'assigner_id', 'status_id');

}