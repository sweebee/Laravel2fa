<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use Illuminate\Support\ServiceProvider;

class Laravel2faServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Wiebenieuwenhuis\Laravel2fa\Laravel2faController');
	    $this->loadViewsFrom(__DIR__.'/resources/views', '2fa');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
	    $this->loadTranslationsFrom(realpath(__DIR__.'/resources/lang'), '2fa');
    	$this->publishes([
		    __DIR__.'/config/2fa.php' => config_path('2fa.php'),
	    ]);

	    $this->mergeConfigFrom(
		    __DIR__.'/config/2fa.php', '2fa'
	    );

	    $this->publishes([
		    __DIR__ . '/migrations' => $this->app->databasePath() . '/migrations'
	    ], 'migrations');

	    Route::group(['middleware' => config('2fa.middleware', [])], function () {
		    $this->loadRoutesFrom(__DIR__.'/routes.php');
	    });
    }
}
