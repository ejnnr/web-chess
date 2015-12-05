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
}
