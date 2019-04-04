# Laravel 2fa

### Installation:

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

This package has its own views, to override these views you have to override the routes.

### Custom setup

Create a view with atleast these elements:

```html
<form method="post" action="/2fa/setup">
  <img src="{{ Wiebenieuwenhuis\Laravel2fa\Laravel2fa::generateQrCode() }}" alt="">
  {{ csrf_field }}
  <input type="text" name="2fa_code">
  <button type="submit">Validate</button>
</form>
```

### Custom validation

Create a view with atleast these elements:

```html
<form method="post" action="/2fa/auth">
  {{ csrf_field }}
  <input type="text" name="2fa_code">
  <button type="submit">Validate</button>
</form>
```

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
