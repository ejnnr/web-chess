<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use Exception;
use App\Chess\JCFGame;

class CustomValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
		Validator::extend('jcf', function($attribute, $value, $parameters, $validator) {
			$game = app(JCFGame::class);
			try {
				$game->loadJCF($value);
			} catch (Exception $e) {
				return false;
			}

			return true;
		});
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
