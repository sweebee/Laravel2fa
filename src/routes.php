<?php

Route::get( 'setup', 'Laravel2faController@setup' )->name( '2fa::setup' );
Route::post( 'setup', 'Laravel2faController@validateSetup' )->name( '2fa::validate-setup' );
Route::get( 'auth', 'Laravel2faController@auth' )->name( '2fa::auth' );
Route::post( 'auth', 'Laravel2faController@validateAuth' )->name( '2f::validate-auth' );
Route::get( 'disable', 'Laravel2faController@disable' )->name( '2fa::disable' );
