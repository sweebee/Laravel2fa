<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use Closure;

class Middleware
{
	public function handle($request, Closure $next)
	{
		// Check url is excluded
		if(in_array($request->route()->uri, config('2fa.exclude_urls', []))){
		    return $next($request);
		}

		// Check if 2fa required and not enabled
		if(config('2fa.required') && !Laravel2fa::enabled()){
		    return redirect(route('2fa::setup'));
		}

		// Check if the user is authenticated by 2fa
        	if(Laravel2fa::authenticated()){
            	    return $next($request);
        	}

		// Not authenticated, redirect to 2fa auth route
		return redirect(config('2fa.auth_route'));
	}
}
