<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roleuser extends Model 
{

    protected $table = 'role_user';
    public $timestamps = false;
    protected $fillable = array('user_id', 'role_id');

}