<?php

return [

	// Prefix for the default routes
	'prefix' => '2fa',

	// The url with the form for authentication, override to use your own
	'auth_route' => '/2fa/auth',

	// The url to redirect to after a successful validation
	'redirect' => '/home',

	// The authentication guard to use for logged in users
	'guard' => 'web',

	// The name of the input field for the authentication code
	'code_input_name' => '2fa_code',

	// The name of the checkbox field to remember the device
	'remember_input_name' => '2fa_remember',

	// Time to remember the device
	'remember_time' => 3600 * 24 * 7,

	// Middlewares to use in the default routes
	'middleware' => [
		'web',
	]

];
