<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use Closure;

class Middleware
{
	public function handle($request, Closure $next)
	{
		// Check if the user is authenticated by 2fa
		if(Laravel2fa::authenticated() || in_array($request->route()->uri, config('2fa.exclude_urls', []))){
			return $next($request);
		}

		// Not authenticated, redirect to 2fa auth route
		return redirect(config('2fa.auth_route'));
	}
}
