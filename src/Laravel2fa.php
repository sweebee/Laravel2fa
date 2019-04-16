<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use BaconQrCode\Renderer\Image\Svg;
use BaconQrCode\Writer;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Cache;

class Laravel2fa {

	/**
	 * Generate a secret key
	 *
	 * @param BaseModel|null $model
	 *
	 * @return string
	 */
	public static function generateSecret(BaseModel $model = null)
	{
		$model = self::getModel($model);

		// Get or create settings for the authenticated user
		if(!$settings = self::getSettings($model)){
			$settings = new Settings();
			$settings->model_type = get_class($model);
			$settings->model_id = $model->id;
			$settings->secret = encrypt((new Google2FA())->generateSecretKey());
			$settings->save();
		}

		// return the secret
		return decrypt($settings->secret);
	}

	/**
	 * Get the QR code (base64 encoded svg)
	 *
	 * @param BaseModel|null $model
	 *
	 * @return string
	 */
	public static function generateQrCode(BaseModel $model = null)
	{
		$model = self::getModel($model);

		// Get/generate the secret
		$secret = self::generateSecret($model);

		// Generate a 2fa url
		$url = (new Google2FA())->getQRCodeUrl(
			config('app.name'),
			$model->email,
			$secret
		);

		// Generate the svg
		$renderer = new Svg();
		$renderer->setHeight(256);
		$renderer->setWidth(256);
		$renderer->setMargin(1);
		$writer = new Writer($renderer);
		$svg = $writer->writeString($url);

		return 'data:image/svg+xml;base64,' . base64_encode($svg);
	}

	/**
	 * Enable 2fa for the authenticated user
	 *
	 * @param BaseModel|null $model
	 */
	public static function enable(BaseModel $model = null)
	{
		$model = self::getModel($model);
		if(!$settings = self::getSettings($model)){
			abort(403, '2fa not set');
		}
		$settings->enabled = true;
		$settings->save();
	}

	/**
	 * Disable 2fa for the authenticated user
	 *
	 * @param BaseModel|null $model
	 */
	public static function disable(BaseModel $model = null)
	{
		$model = self::getModel($model);
		$settings = self::getSettings($model);

		Cookie::forget(self::cookieName($settings));
		Settings::where('model_type', get_class($model))->where('model_id', $model->id)->delete();
	}

	/**
	 * Check if 2fa is enabled for the authenticated user
	 *
	 * @param BaseModel|null $model
	 *
	 * @return bool
	 */
	public static function enabled(BaseModel $model = null)
	{
		$model = self::getModel($model);
		$settings = self::getSettings($model);
		return $settings->enabled ?? false;
	}

	/**
	 * Validate the user's input with his secret key
	 *
	 * @param $code
	 * @param BaseModel|null $model
	 *
	 * @return bool
	 */
	public static function validate($code, BaseModel $model = null)
	{
		$code = str_replace(' ','', trim($code));
		// If the code is already cached, its invalid
		if(Cache::has('2fa_' . $code)){
			return false;
		}

		$model = self::getModel($model);

		$settings = self::getSettings($model);

		// Cache the code for 5 minutes so it wont work again
		Cache::put('2fa_' . $code, true, 60 * 5);

		// Verify the code
		try {
			$valid = (bool) ( new Google2FA() )->verifyKey( decrypt($settings->secret), $code, 1 );
		} catch (\Exception $e){
			$valid = false;
		}

		if($valid) {
			session( [ '2fa_authenticated' => true ] );
		}

		return $valid;
	}

	/**
	 * Remember for the current device
	 *
	 * @param BaseModel|null $model
	 */
	public static function remember(BaseModel $model = null)
	{
		$model = self::getModel($model);
		$settings = self::getSettings($model);
		Cookie::queue(Cookie::make(self::cookieName($settings), $settings->remember_token(), 3600 * 24 * 7));
	}

	/**
	 * Check if authenticated
	 *
	 * @return bool
	 */
	public static function authenticated(BaseModel $model = null)
	{
		$model = self::getModel($model);

		$settings = self::getSettings($model);

		if(!$settings || !$settings->enabled){
			return true;
		}

		// Check if authenticated by session
		if(session('2fa_authenticated')){
			return true;
		}

		// Check if authenticated by cookie (remember)
		$cookieName = self::cookieName($settings);

		if($token = Cookie::get($cookieName)){
			if($token === $settings->remember_token){
				session(['2fa_authenticated' => true]);
				return true;
			}
			// Invalid cookie, delete it
			Cookie::forget($cookieName);
		}

		return false;
	}

	/**
	 * Get the authenticated user model or a specific one
	 *
	 * @param $model
	 *
	 * @return mixed
	 */
	private static function getModel($model)
	{
		$model = $model ?? Auth::guard(config('2fa.guard'))->user();

		if(!$model){
			abort(403, 'User not logged in');
		}

		return $model;
	}

	/**
	 * Get the 2fa settings for the authenticated user
	 *
	 * @param BaseModel $model
	 *
	 * @return Settings
	 */
	private static function getSettings(BaseModel $model)
	{
		return Settings::where('model_type', get_class($model))->where('model_id', $model->id)->first();
	}


	/**
	 * Get the cookie name for remembering the device
	 *
	 * @param Settings $settings
	 *
	 * @return string
	 */
	private static function cookieName(Settings $settings)
	{
		return '2fa_remember_' . str_replace('=', '', base64_encode($settings->model_id . $settings->model_type));
	}

}
