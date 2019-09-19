<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

class Project extends Model
{
    use PresentableTrait;
    protected $presenter = 'App\Presenters\ProjectPresenter';

    protected $table = 'projects';
    public $timestamps = true;
    protected $fillable = array('name', 'start_date', 'end_date', 'project_code');

    /**
     * Get the allocations for the project.
     */
    public function allocations()
    {
        return $this->hasMany('App\Allocation');
    }
    /**
     * Get project resources.
     */
    public function resources()
    {
        return $this->hasMany('App\Projectroleuser');
    }
    public function skills()
    {
      return $this->hasMany('App\Skillsused','item_id','id')->where('item_type', 'project');
    }
}
