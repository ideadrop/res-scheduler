<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model 
{

    protected $table = 'tags';
    public $timestamps = true;
    protected $fillable = array('name', 'author_id');

    public function getTaggedProjects(){
        return $this->hasMany('App\Tagsused')->where('tag_type','=','project');
    }
    public function getTaggedResources(){
        return $this->hasMany('App\Tagsused')->where('tag_type','=','user');
    }
}