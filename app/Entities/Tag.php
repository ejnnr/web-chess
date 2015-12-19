<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Tag extends Model implements Transformable
{
    use TransformableTrait;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
    protected $fillable = ['owner_id', 'name', 'public'];

	public function owner()
	{
		return $this->belongsTo('App\Entities\User', 'owner_id'); // use owner_id in databases
	}

	public function games()
	{
		return $this->belongsToMany('App\Entities\Game')->withTimestamps();
	}

	public function sharedWith()
	{
		return $this->belongsToMany('App\Entities\User', 'shared_tags')->withTimestamps()->withPivot('access_level');
	}

	public function share($userId, $accessLevel)
	{
		$this->sharedWith()->attach($userId, ['access_level' => $accessLevel]);
	}
}
