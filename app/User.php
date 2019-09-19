<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Laracasts\Presenter\PresentableTrait;

class User extends Authenticatable
{
    use EntrustUserTrait;
    use PresentableTrait;

    protected $presenter = 'App\Presenters\UserPresenter';

    protected $table = 'users';
    public $timestamps = true;
    protected $fillable = array('email', 'password', 'remember_token','disabled','allocatable');

    public function profile()
	{
		return $this->hasOne('App\Profile', 'user_id', 'id');
	}
    public function allocations($projectId = false)
    {
        if($projectId){
            return $this->hasMany('App\Allocation','assignee_id','id')
                ->where('project_id','=',$projectId);
        }else{
            return $this->hasMany('App\Allocation','assignee_id','id');
        }

    }
    public function assignedProjects()
    {
       return $this->hasMany('App\Projectroleuser','user_id','id');
    }
    public function projects()
    {
        return $this->hasMany('App\Project');
    }
    public function skills()
    {
      return $this->hasMany('App\Skillsused','item_id','id')->where('item_type', 'user');
    }
    public function scopeEnabled($query){
        return $query->where('users.disabled','=',0);
    }
    public function scopeAllocatable($query)
    {
        return $query->where('users.allocatable','=',1);
    }




}
