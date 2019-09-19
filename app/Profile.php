<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    protected $table = 'profiles';
    public $timestamps = true;
    protected $fillable = array('first_name', 'last_name', 'company', 'designation', 'address_line1', 'address_line2', 'phone', 'city', 'state', 'country', 'zipcode', 'user_id');

	protected function User()
	{
		return $this->belongsTo('App\User', 'id', 'user_id');
	}
  public function designationName()
  {
    return $this->belongsTo('App\Usertype', 'designation', 'id');
  }
}
