<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Database extends Model {

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['owner_id', 'public', 'name'];

	public function owner()
	{
		return $this->belongsTo('App\User', 'owner_id'); // use owner_id in databases
	}

	public function games()
	{
		return $this->hasMany('App\Game');
	}

	public function sharedWith()
	{
		return $this->belongsToMany('App\User', 'shared_databases')->withTimestamps()->withPivot('access_level');
	}

	public function share($userId, $accessLevel)
	{
		$this->sharedWith()->attach($userId, ['access_level' => $accessLevel]);
	}
}
