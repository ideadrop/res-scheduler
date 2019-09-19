<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projectroleuser extends Model 
{

    protected $table = 'projects_used';
    public $timestamps = true;
    protected $fillable = array('user_id', 'project_id');

    /**
     * Get resource users.
     */
    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }
    /**
     * Get resource users.
     */
    public function project()
    {
        return $this->hasOne('App\Project','id','project_id');
    }

}