<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Usertype extends Model
{

    protected $table = 'user_types';
    public $timestamps = true;
    protected $fillable = array('name');

}
