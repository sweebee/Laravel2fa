## Laravel 2fa

Installation:

```
composer require wiebenieuwenhuis/laravel-2fa
php artisan vendor:publish --provider="Wiebenieuwenhuis\Laravel2fa\Laravel2faServiceProvider"
php artisan migrate
```

Add the middleware to the routeMiddlewares in the Kernel:

```
protected $routeMiddleware = [
    ...
    '2fa' => \Wiebenieuwenhuis\Laravel2fa\Middleware::class,
]
```

Make sure you add the ```2fa``` middleware to your routes. and have set the right variables in the ```config/2fa.php``` file.

### enable 2fa
```
/2fa/setup // route("2fa::setup")
```

### disable 2fa
```
/2fa/disable // route("2fa::disable")
```
