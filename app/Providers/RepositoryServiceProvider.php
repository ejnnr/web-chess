<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
	/**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\UserRepository', 'App\Repositories\UserRepositoryEloquent');
        $this->app->bind('App\Repositories\TagRepository', 'App\Repositories\TagRepositoryEloquent');
        $this->app->bind('App\Repositories\GameRepository', 'App\Repositories\GameRepositoryEloquent');
    }

	/**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
			'App\Repositories\UserRepository',
			'App\Repositories\TagRepository',
			'App\Repositories\GameRepository',
		];
    }
}
