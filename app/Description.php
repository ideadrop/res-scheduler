<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Description extends Model 
{

    protected $table = 'descriptions';
    public $timestamps = true;
    protected $fillable = array('item_id', 'item_type', 'value');
}