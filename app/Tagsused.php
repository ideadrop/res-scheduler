<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tagsused extends Model 
{

    protected $table = 'tags_used';
    public $timestamps = true;
    protected $fillable = array('tag_type', 'tag_id', 'target_id');

    public function project(){
        return $this->hasOne('App\Project','id','target_id');
    }
    public function resource(){
        return $this->hasOne('App\User','id','target_id');
    }

}