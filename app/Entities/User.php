<?php namespace App\Entities;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, Transformable
{

	use Authenticatable, Authorizable, CanResetPassword, TransformableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	public function setPasswordAttribute($password)
	{
		$this->attributes['password'] = \Hash::make($password);
	}

	public function tags()
	{
		return $this->hasMany('App\Entities\Tag', 'owner_id');
	}

	public function sharedTags()
	{
		return $this->belongsToMany('App\Entities\Tag', 'shared_tags')->withTimestamps()->withPivot('access_level');
	}

	public function sharedGames()
	{
		return $this->belongsToMany('App\Entities\Game', 'shared_games')->withTimestamps()->withPivot('access_level');
	}
}
