<?php namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Chess\BCFGame;

class Game extends Model implements Transformable
{
	use TransformableTrait;

    protected $fillable = ['bcf'];

	public function tags()
	{
		return $this->belongsToMany('App\Entities\Tag')->withTimestamps();
	}

	public function sharedWith()
	{
		return $this->belongsToMany('App\Entities\User', 'shared_games')->withTimestamps()->withPivot('access_level');
	}

	public function share($userId, $accessLevel)
	{
		$this->sharedWith()->attach($userId, ['access_level' => $accessLevel]);
	}

	public function setGameAttribute(BCFGame $game)
	{
		$this->bcf = $game->getBCF();
	}
	
	public function getGameAttribute()
	{
		$game = \App::make(BCFGame::class);
		$game->loadBCF(isset($this->bcf) ? $this->bcf : '');
		return $game;
	}
}
