<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skillsused extends Model
{

    protected $table = 'skills_used';
    public $timestamps = true;
    protected $fillable = array('skill_id', 'item_type', 'item_id');

    /**
     * Method to get skill used in projects
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project(){
        return $this->hasOne('App\Project','id','item_id');
    }
    /**
     * Method to get skill used in projects
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function resource(){
        return $this->hasOne('App\User','id','item_id');
    }
    public function skill(){
        return $this->hasOne('App\Skill','id','skill_id');
    }





}
