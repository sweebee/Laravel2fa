# Laravel 2fa

### Installation:

```
composer require wiebenieuwenhuis/laravel2fa
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

By route:
```
/2fa/setup // route("2fa::setup")
```

### disable 2fa

By route:
```
/2fa/disable // route("2fa::disable")
```

By API:
```
\Wiebenieuwenhuis\Laravel2fa\Laravel2fa::disable()
```


## Custom views

This package has its own views, these are published in your resource folder ```resources/views/vendor/2fa```. You can modify these to your needs.

## Advanced

Generate a secret for a user:

```Wiebenieuwenhuis\Laravel2fa\Laravel2fa::generateSecret()```

Generate a QR code for setup:

```Wiebenieuwenhuis\Laravel2fa\Laravel2fa::generateQrCode()```

Validate a code:

```Wiebenieuwenhuis\Laravel2fa\Laravel2fa::validate($code)```

To enable 2fa after setup you can enable it, make sure you've created it first by creating a secret or generating a QR code.

```Wiebenieuwenhuis\Laravel2fa\Laravel2fa::enable()```

Disable the 2fa:

```Wiebenieuwenhuis\Laravel2fa\Laravel2fa::disable()```

To check if the user has 2fa enabled:

```Wiebenieuwenhuis\Laravel2fa\Laravel2fa::enabled()```
