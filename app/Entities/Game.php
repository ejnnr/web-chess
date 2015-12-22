<?php namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Presentable;
use Prettus\Repository\Traits\PresentableTrait;
use App\Chess\BCFGame;

class Game extends Model implements Presentable
{
	use PresentableTrait;

    protected $fillable = ['jcf', 'owner_id'];

	public function tags()
	{
		return $this->belongsToMany('App\Entities\Tag')->withTimestamps();
	}

	public function owner()
	{
		return $this->belongsTo('App\Entities\User', 'owner_id'); // use owner_id in databases
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

	public function setJCFAttribute($jcf)
	{
		$this->bcf = app(BCFGame::class)->loadJCF($jcf)->getBCF();
	}

	public function getJCFAttribute()
	{
		return app(BCFGame::class)->loadBCF($this->bcf)->getJCF();
	}
}
