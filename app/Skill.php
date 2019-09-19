<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

class Skill extends Model 
{
    use PresentableTrait;
    protected $presenter = 'App\Presenters\SkillPresenter';

    protected $table = 'skills';
    public $timestamps = true;
    protected $fillable = array('name');

    /**
     * Method to get skill used in projects
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usedProjects(){
        return $this->hasMany('App\Skillsused')
            ->where('item_type','=','project');
    }

    /**
     * Method to get skill used in projects
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usedResources(){
        return $this->hasMany('App\Skillsused')
            ->where('item_type','=','user');
    }

}