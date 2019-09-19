<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

class Allocation extends Model
{
    use PresentableTrait;
    protected $presenter = 'App\Presenters\AllocationPresenter';

    protected $table = 'allocations';
    public $timestamps = true;
    protected $fillable = array('project_id', 'start_date', 'end_date', 'assignee_id', 'assigner_id', 'allocation_type', 'allocation_value');

    /**
     * Get the project that owns the allocation.
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Get the assignee
     */
    public function assignee()
    {
        return $this->hasOne('App\User','id','assignee_id');
    }
    /**
     * Get the profile of the assignee
     */
    public function assigneeProfile()
    {
        return $this->hasOne('App\Profile','user_id','assignee_id');
    }
    /**
     * Get the assigner
     */
    public function assigner()
    {
        return $this->hasOne('App\User','id','assigner_id');
    }
    /**
     * Get the profile of the assigner
     */
    public function assignerProfile()
    {
        return $this->hasOne('App\Profile','user_id','assigner_id');
    }
}
