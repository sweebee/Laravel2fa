<?php

namespace Wiebenieuwenhuis\Laravel2fa;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Cache;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class Laravel2fa {

	/**
	 * Generate a secret key
	 *
	 * @return string
	 */
	public static function generateSecret(BaseModel $model = null)
	{
		$model = self::getModel($model);

		if(!$settings = self::getSettings($model)){
			$settings = new Model();
			$settings->model_type = get_class($model);
			$settings->model_id = $model->id;
			$settings->secret = encrypt((new Google2FA())->generateSecretKey());
			$settings->save();
		}

		return decrypt($settings->secret);
	}

	/**
	 * @param BaseModel|null $model
	 *
	 * @return mixed
	 */
	public static function generateQrCode(BaseModel $model = null)
	{
		$model = self::getModel($model);

		$secret = self::generateSecret($model);

		$url = (new Google2FA())->getQRCodeUrl(
			config('app.name'),
			$model->email,
			$secret
		);

		$renderer = new ImageRenderer(
			new RendererStyle(400),
			new ImagickImageBackEnd()
		);

		$writer = new Writer($renderer);
		return 'data:image/png;base64,' . base64_encode($writer->writeString($url));
	}

	/**
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
	 * @param BaseModel|null $model
	 */
	public static function disable(BaseModel $model = null)
	{
		$model = self::getModel($model);
		$settings = self::getSettings($model);

		Cookie::forget(self::cookieName($settings));
		Model::where('model_type', get_class($model))->where('model_id', $model->id)->delete();
	}

	/**
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
	 * @param $secret
	 * @param $code
	 *
	 * @return bool
	 */
	public static function validate($code, BaseModel $model = null)
	{
		$code = str_replace(' ','', trim($code));
		// If the code is already cached, its invalid
		if(!Cache::missing('2fa_' . $code)){
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

	public static function remember(BaseModel $model = null)
	{
		$model = self::getModel($model);
		$settings = self::getSettings($model);
		if(!$settings->remember_token) {
			$settings->remember_token = (string) Str::uuid();
			$settings->save();
		}
		Cookie::queue(Cookie::make(self::cookieName($settings), $settings->remember_token, 3600 * 24 * 7));
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

		if(session('2fa_authenticated')){
			return true;
		}

		$cookieName = self::cookieName($settings);

		if($token = Cookie::get($cookieName)){
			if($token === $settings->remember_token){
				session(['2fa_authenticated' => true]);
				return true;
			}
			Cookie::forget($cookieName);
		}

		return false;
	}

	/**
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
	 * @param BaseModel $model
	 *
	 * @return mixed
	 */
	private static function getSettings(BaseModel $model)
	{
		return Model::where('model_type', get_class($model))->where('model_id', $model->id)->first();
	}


	/**
	 * @param Model $settings
	 *
	 * @return string
	 */
	private static function cookieName(Model $settings)
	{
		return '2fa_remember_' . str_replace('=', '', base64_encode($settings->model_id . $settings->model_type));
	}

}
