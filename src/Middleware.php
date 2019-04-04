<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class Middleware
{
	public function handle($request, Closure $next)
	{
		if(Laravel2fa::authenticated()){
			return $next($request);
		}

		return redirect(config('2fa.auth_route'));
	}
}
