<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['database_id', 'bcf'];

	public function database()
	{
		return $this->belongsTo('App\Database');
	}

	public function sharedWith()
	{
		return $this->belongsToMany('App\User', 'shared_games')->withTimestamps()->withPivot('access_level');
	}

	public function share($userId, $accessLevel)
	{
		$this->sharedWith()->attach($userId, ['access_level' => $accessLevel]);
	}
}
