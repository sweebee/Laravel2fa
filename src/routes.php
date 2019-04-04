<?php

Route::group(['middleware' => 'web'], function() {
	Route::get( '2fa/setup', 'Wiebenieuwenhuis\Laravel2fa\Laravel2faController@setup' )->name('2fa::setup');
	Route::post( '2fa/setup', 'Wiebenieuwenhuis\Laravel2fa\Laravel2faController@validateSetup' );
	Route::get( '2fa/auth', 'Wiebenieuwenhuis\Laravel2fa\Laravel2faController@auth' );
	Route::post( '2fa/auth', 'Wiebenieuwenhuis\Laravel2fa\Laravel2faController@validateAuth' );
	Route::get('2fa/disable', 'Wiebenieuwenhuis\Laravel2fa\Laravel2faController@disable')->name('2fa::disable');
});
