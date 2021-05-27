<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class Laravel2faServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Load the translations
		$this->loadTranslationsFrom(realpath(__DIR__.'/resources/lang'), '2fa');

		// Load the default views/forms for setup and authentication
		$this->loadViewsFrom(__DIR__.'/resources/views', '2fa');

		$this->registerPublishing();

		$this->registerRoutes();
	}

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
	    // Merge the config file
	    $this->mergeConfigFrom(
		    __DIR__.'/config/2fa.php', '2fa'
	    );

        $this->app->make('Wiebenieuwenhuis\Laravel2fa\Laravel2faController');
    }

    private function registerPublishing()
    {
	    // Publish config files
	    $this->publishes([
		    __DIR__.'/config/2fa.php' => config_path('2fa.php'),
	    ], 'laravel2fa-config');

	    // Publish migration files
	    $this->publishes([
		    __DIR__ . '/migrations' => $this->app->databasePath() . '/migrations'
	    ], 'laravel2fa-migrations');
	    
	    // Publish views
	    $this->publishes([
	        __DIR__.'/resources/views' => resource_path('views/vendor/2fa'),
	    ]);
    }

	/**
	 * Register the default routes
	 */
	private function registerRoutes()
    {
	    $config = [
		    'middleware' => config('2fa.middleware', []),
		    'prefix'     => config('2fa.prefix', '2fa'),
		    'namespace'  => 'Wiebenieuwenhuis\Laravel2fa'
	    ];

	    Route::group($config, function () {
		    $this->loadRoutesFrom(__DIR__.'/routes.php');
	    });
    }
}
